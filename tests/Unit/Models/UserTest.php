<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    public function testItDeletesTheOldAvatarWhenTheNewAvatarIsAdded()
    {
        Storage::fake('avatars');
        $user = User::factory()->create();
        $oldAvatarFile = UploadedFile::fake()->image('avatar.jpg', 200, 200)->size(200);
        $user->avatar = $oldAvatarFile;
        $user->save();

        // Make sure the uploaded file was stored
        $oldAvatarFilename = $user->avatar;
        Storage::disk('public')->assertExists('avatars/' . $oldAvatarFilename);

        // Update the image
        $newAvatarFile = UploadedFile::fake()->image('avatar2.jpg', 200, 200)->size(200);
        $user->avatar = $newAvatarFile;
        $user->save();

        // Ensure that the field was updated
        $newAvatarFilename = $user->avatar;
        $this->assertNotEquals($oldAvatarFilename, $newAvatarFilename);
        // Assert that the new image was stored
        Storage::disk('public')->assertExists('avatars/' . $newAvatarFilename);
        // Assert that the old image was deleted
        Storage::disk('public')->assertMissing('avatars/' . $oldAvatarFilename);
    }

    public function testItDeletesTheAvatarFileWhenClearing()
    {
        Storage::fake('avatars');
        $user = User::factory()->create();
        $oldAvatarFile = UploadedFile::fake()->image('avatar.jpg', 200, 200)->size(200);
        $user->avatar = $oldAvatarFile;
        $user->save();

        // Make sure the uploaded file was stored
        $oldAvatarFilename = $user->avatar;
        Storage::disk('public')->assertExists('avatars/' . $oldAvatarFilename);

        // Update the image
        $user->avatar = null;
        $user->save();

        // Ensure that the field was updated
        $newAvatarFilename = $user->avatar;
        $this->assertNotEquals($oldAvatarFilename, $newAvatarFilename);
        // Assert that the old image was deleted
        Storage::disk('public')->assertMissing('avatars/' . $oldAvatarFilename);
    }
}
