<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\TaskType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $taskType = TaskType::inRandomOrder()->first();
        if (!$taskType) {
            $taskType = TaskType::factory()->create();
        }
        return [
            'description' => $this->faker->sentence,
            'type_id' => TaskType::inRandomOrder()->first()->id,
            'title' => $this->faker->sentence(rand(3, 10)),
            'priority' => rand(0, 5),
            'blocked' => false,
        ];
    }
}
