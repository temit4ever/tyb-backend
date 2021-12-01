<?php

namespace Tests\Feature;

use App\Models\TaskType;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTypeControllerTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testItListsAllTaskTypes()
    {
        $this->seedTaskTypes();
        $response = $this->json('GET', $this->apiPrefix . 'task-types');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'title',
                        'slug',
                    ]
                ]
            ]);
        TaskType::all()->each(function ($taskType) use ($response) {
            $response->assertJsonFragment([
                'title' => $taskType->title,
                'slug' => $taskType->slug,
            ]);
        });
    }

    public function testItGetsOneTaskType()
    {
        $this->seedTaskTypes();
        $response = $this->json('GET', $this->apiPrefix . 'task-types/2');
//        dd(json_decode($response->getContent()));

        $taskType = TaskType::find(2);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'slug',
                ]
            ])
            ->assertJsonFragment([
                'slug' => $taskType->slug,
                'title' => $taskType->title,
            ]);
    }

    public function testItDeletesOneTaskType()
    {
        $this->seedTaskTypes();
        $response = $this->json('DELETE', $this->apiPrefix . 'task-types/2');

        $response->assertStatus(200);
        $this->assertDatabaseMissing('task_types', ['id' => 2]);
        $this->assertDatabaseHas('task_types', ['id' => 1]);
        $this->assertDatabaseHas('task_types', ['id' => 3]);
    }

    public function testItValidatesTheRequestWhenCreating()
    {
        $response = $this->json('POST',
            $this->apiPrefix . 'task-types',
            []
        );

        $response->assertStatus(422)
            ->assertSee('The title field is required.')
            ->assertSee('The slug field is required.');
    }


    public function testItCreatesATaskType()
    {
        $data = [
            'title' => 'Test Created 1',
            'slug' => 'test-created-1'
        ];
        $response = $this->json('POST', $this->apiPrefix . 'task-types', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'slug',
                ]
            ])
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('task_types', [
            'title' => $data['title'],
            'slug' => $data['slug'],
        ]);
    }

    public function testItValidatesTheRequestWhenUpdating()
    {
        $this->seedTaskTypes();
        $response = $this->json('PUT',
            $this->apiPrefix . 'task-types/2',
            []
        );

        $response->assertStatus(422)
            ->assertSee('The title field is required.')
            ->assertSee('The slug field is required.');
    }

    public function testItRequiresTheTitleToHaveThreeCharactersWhenUpdating()
    {
        $this->seedTaskTypes();
        $response = $this->json('PUT',
            $this->apiPrefix . 'task-types/2',
            ['title' => '12', 'slug' => '12345']
        );

        $response->assertStatus(422)
            ->assertSee('The title must be at least 3 characters.');
    }

    public function testItUpdatesATaskType()
    {
        $this->seedTaskTypes();
        $data = [
            'title' => 'Test Updated 1',
            'slug' => 'test-updated-1',
        ];
        $response = $this->json('PUT', $this->apiPrefix . 'task-types/3', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'slug',
                    'title',
                ]
            ])
            ->assertJsonFragment(['id' => 3])
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('task_types', [
            'id' => 3,
            'title' => $data['title'],
            'slug' => $data['slug'],
        ]);
    }


    private function seedTaskTypes()
    {
        TaskType::factory()->count(4)->create();
    }
}
