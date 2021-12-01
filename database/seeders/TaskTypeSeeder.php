<?php

namespace Database\Seeders;

use App\Models\TaskType;
use Illuminate\Database\Seeder;

class TaskTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ['slug' => "ticket", "title" => "Ticket"],
            ['slug' => "pair-programming", "title" => "Pair Programming"],
            ['slug' => "meeting", "title" => "Meeting"],
            ['slug' => "triage", "title" => "Triage"],
            ['slug' => "grooming", "title" => "Grooming"],
            ['slug' => "review", "title" => "Review"],
            ['slug' => "training", "title" => "Training"],
            ['slug' => "ted-talk", "title" => "Ted Talk"],
            ['slug' => "planning", "title" => "Planning"],
        ];
        foreach ($types as $type) {
            TaskType::factory()->create($type);
        }
    }
}
