<?php

namespace Database\Factories;

use App\Models\Sprint;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class SprintFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sprint::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startDate = Carbon::now()->subWeeks(rand(-10, 10))->startOfWeek();
        $endDate = clone $startDate;
        $endDate->addWeek(1);
        return [
            'title' => $this->faker->sentence(rand(3, 6)),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }
}
