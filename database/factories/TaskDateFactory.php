<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\TaskDate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskDateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskDate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = Carbon::today()->subDay(rand(2, 14));
        return [
            'task_id' => TaskDate::factory(),
            'activity_date' => $date,
        ];
    }
}
