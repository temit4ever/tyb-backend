<?php

namespace Database\Seeders;

use App\Models\Sprint;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SprintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $startDate = Carbon::now()->addWeeks(3)->startOfWeek();
        $sprintsToCreate = 20;
        for ($i = 0; $i < $sprintsToCreate ; $i++) {
            $startDate->subWeeks(2);
            $endDate = clone $startDate;
            $endDate->addWeek(2)->subDay();
            Sprint::factory()->create([
                'title' => 'Sprint ' . ($sprintsToCreate - $i),
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);
        }
    }
}
