<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskDate;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        for($i = 0; $i < 20 ; ++$i) {
            $userSubset = $users->random(rand(1, 4));
            Task::factory()
                ->count(5)
                ->hasAttached($userSubset)
                ->has(TaskDate::factory()->count(rand(0, 5)))
                ->create();
        }
    }
}
