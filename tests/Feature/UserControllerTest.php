<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testItListsAllUsers()
    {
        $this->seedUsers();
        $response = $this->json('GET', $this->apiPrefix . 'users');
//        dd(json_decode($response->getContent()));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'title',
                    ]
                ]
            ])
            ->assertJsonFragment(['title' => 'User 1'])
            ->assertJsonFragment(['avatar' => null])
            ->assertJsonFragment(['avatar_url' => null]);
    }

    public function testItGetsOneUser()
    {
        $this->seedUsers();
        $response = $this->json('GET', $this->apiPrefix . 'users/2');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'title',
                ]
            ])
            ->assertJsonFragment(['title' => 'User 2']);
    }

    public function testItReturnsAnAvatarPathIfPresent()
    {
        $this->seedUsers(true);
        // get the created avatar filename
        $user = User::find(2);
        $avatarFilename = $user->avatar;
        $response = $this->json('GET', $this->apiPrefix . 'users/2');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'title',
                ]
            ])
            ->assertJsonFragment(['title' => 'User 2'])
            ->assertJsonFragment(['avatar' => $avatarFilename])
            ->assertJsonFragment(['avatar_url' => '/storage/avatars/' . $avatarFilename]);
    }

    public function testItDeletesOneUser()
    {
        $this->seedUsers();
        $response = $this->json('DELETE', $this->apiPrefix . 'users/2');

        $response->assertStatus(200);
        $this->assertSoftDeletedInDatabase('users', ['id' => 2]);
        $this->assertNotSoftDeletedInDatabase('users', ['id' => 1]);
        $this->assertNotSoftDeletedInDatabase('users', ['id' => 3]);
    }

    public function testItValidatesTheRequestWhenCreating()
    {
        $response = $this->json('POST',
            $this->apiPrefix . 'users',
            []
        );

        $response->assertStatus(422)
            ->assertSee('The title field is required.');
    }

    public function testItCreatesAUser()
    {
        $data = [
            'title' => 'Test Created 1',
        ];
        $response = $this->json('POST', $this->apiPrefix . 'users', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'created_at',
                    'updated_at',
                ]
            ])
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('users', ['title' => $data['title']]);
    }

    public function testItValidatesTheRequestWhenUpdating()
    {
        $this->seedUsers();
        $response = $this->json('PUT',
            $this->apiPrefix . 'users/2',
            []
        );

        $response->assertStatus(422)
            ->assertSee('The title field is required.');
    }

    public function testItRequiresTheTitleToHaveThreeCharactersWhenUpdating()
    {
        $this->seedUsers();
        $response = $this->json('PUT',
            $this->apiPrefix . 'users/2',
            ['title' => '12']
        );

        $response->assertStatus(422)
            ->assertSee('The title must be at least 3 characters.');
    }

    public function testItUpdatesAUser()
    {
        $this->seedUsers();
        $data = [
            'title' => 'Test Updated 1',
        ];
        $response = $this->json('PUT', $this->apiPrefix . 'users/3', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'created_at',
                    'updated_at',
                ]
            ])
            ->assertJsonFragment(['id' => 3])
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('users', ['id' => 3, 'title' => $data['title']]);
    }

    public function testTheAvatarMustBeAnImage()
    {
        Storage::fake('avatars');

        $data = [
            'title' => 'Test Avatar 1',
            'avatar' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
        ];
        $response = $this->json('POST', $this->apiPrefix . 'users', $data);
        $response->assertStatus(422)
            ->assertSee('The avatar must be a file of type: jpeg, png');
    }

    public function testTheAvatarMustHaveAMinimumSize()
    {
        Storage::fake('avatars');

        $data = [
            'title' => 'Test Avatar 1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 20, 20)->size(10),
        ];
        $response = $this->json('POST', $this->apiPrefix . 'users', $data);
        $response->assertStatus(422)
            ->assertSee('The avatar has invalid image dimensions.');
    }

    public function testTheAvatarMustBeUnderAMaximumSize()
    {
        Storage::fake('avatars');

        $data = [
            'title' => 'Test Avatar 1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 2000, 2000)->size(10000),
        ];
        $response = $this->json('POST', $this->apiPrefix . 'users', $data);
        $response->assertStatus(422)
            ->assertSee('The avatar has invalid image dimensions.');
    }

    public function testTheAvatarMustBeUnderAMaximumFileSize()
    {
        Storage::fake('avatars');

        $data = [
            'title' => 'Test Avatar 1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 200, 200)->size(10000000),
        ];
        $response = $this->json('POST', $this->apiPrefix . 'users', $data);
        $response->assertStatus(422)
            ->assertSee('The avatar may not be greater than 1024 kilobytes.');
    }

    public function testItStoresAnAvatarWhenCreating()
    {
        Storage::fake('avatars');

        $data = [
            'title' => 'Test Avatar 1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 100, 100)->size(100),
        ];
        $response = $this->json('POST', $this->apiPrefix . 'users', $data);
//        dd(json_decode($response->getContent()));

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'created_at',
                    'updated_at',
                    'avatar'
                ]
            ])
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('users', ['title' => $data['title']]);
        $result = json_decode($response->getContent(), true);
        $this->assertNotEmpty($result['data']['avatar']);
        Storage::disk('public')->assertExists('avatars/' . $result['data']['avatar']);

    }

    public function testItStoresAnAvatarWhenUpdating()
    {
        Storage::fake('avatars');
        $this->seedUsers();

        $data = [
            'title' => 'Test Avatar Update 1',
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 100, 100)->size(100),
        ];
        $response = $this->json('PATCH', $this->apiPrefix . 'users/1', $data);
//        dd(json_decode($response->getContent()));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'created_at',
                    'updated_at',
                    'avatar'
                ]
            ])
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('users', ['title' => $data['title']]);
        $result = json_decode($response->getContent(), true);
        $this->assertNotEmpty($result['data']['avatar']);
        Storage::disk('public')->assertExists('avatars/' . $result['data']['avatar']);

    }


    private function seedUsers($withAvatars = false)
    {
        for ($s = 1; $s < 4; ++$s) {
            $user = User::factory()->create([
                'title' => 'User ' . $s,
            ]);
            if ($withAvatars) {
                $avatarFile = UploadedFile::fake()->image('avatar.jpg', 200, 200)->size(200);
                $user->avatar = $avatarFile;
                $user->save();
            }
        }
    }
}
