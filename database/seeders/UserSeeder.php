<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Empty the avatars directory
        $file = new Filesystem;
        $file->cleanDirectory('storage/app/public/avatars');

        $users = [
            [
                "title" => "Cliff Brownn",
                "email" => 'cliff@yahoo.com',
                "avatar_filename" => "cliff.png",
                "password" => Hash::make('cliff'),
                "api_token" => Str::random(20)

            ],
            [
              "title" => "Julie Holtzhausen",
              "email" => 'julie@yahoo.com',
              "avatar_filename" => "julie.png",
              "password" => Hash::make('julie'),
              "api_token" => Str::random(20)
            ],
            [
              "title" => "Matthew Turner",
              "email" => 'matthew@yahoo.com',
              "avatar_filename" => "matt.jpeg",
              "password" => Hash::make('matthew'),
              "api_token" => Str::random(20)
            ],
            [
              "title" => "Tayo Yewon",
              "email" => 'tayo@yahoo.com',
              "avatar_filename" => "tayo.jpeg",
              "password" => Hash::make('tayo'),
              "api_token" => Str::random(20)
            ],
            [
              "title" => "Rick Sharp",
              "email" => 'rick@yahoo.com',
              "avatar_filename" => "rick.jpeg",
              "password" => Hash::make('rick'),
              "api_token" => Str::random(20)
            ]
        ];
        foreach ($users as $user) {
            $createdUser = User::factory()->create($user);
            if (isset($user['avatar_filename'])) {
                $file = new UploadedFile('database/seeders/assets/avatars/' . $user['avatar_filename'], $user['avatar_filename']);
                $createdUser->avatar = $file;
                $createdUser->save();
            }
        }
    }
}
