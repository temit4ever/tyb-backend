<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskDate;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskDateControllerTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testItListsAllTaskDates()
    {
        $tasks = $this->seedTaskDates();
        $testTask = $tasks->first();
        $taskId = $testTask->id;
        $response = $this->json('GET', $this->apiPrefix . 'tasks/' . $taskId . '/dates');
//        dd(json_decode($response->getContent()));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'task_id',
                        'activity_date',
                        'recorded_date',
                    ]
                ]
            ]);
        $testTask->taskDates->each(function ($taskDate) use ($response, $testTask) {
            $response->assertJsonFragment([
                'task_id' => (int) $testTask->id,
                'activity_date' => $taskDate->activity_date->toDateString(),
                'recorded_date' => $taskDate->created_at->toDateString(),
            ]);
        });
    }

    public function testItRequiresTheDateFieldWhenCreating()
    {
        $tasks = $this->seedTaskDates();
        $testTask = $tasks->first();

        $url = $this->apiPrefix . 'tasks/' . $testTask->id . '/dates';
        $response = $this->json('POST',
            $url,
            []
        );

        $response->assertStatus(422)
            ->assertSee('The activity date field is required unless replace is in true.');
    }

    public function testItRequiresTheDateFieldToBeAnArrayWhenCreating()
    {
        $tasks = $this->seedTaskDates();
        $testTask = $tasks->first();

        $url = $this->apiPrefix . 'tasks/' . $testTask->id . '/dates';
        $response = $this->json('POST',
            $url,
            ['activity_date' => 'abc123']
        );

        $response->assertStatus(422)
            ->assertSee("The activity date must be an array.");
    }

    public function testItRequiresTheDateFieldToBeAnArrayOfDatesWhenCreating()
    {
        $tasks = $this->seedTaskDates();
        $testTask = $tasks->first();

        $url = $this->apiPrefix . 'tasks/' . $testTask->id . '/dates';
        $response = $this->json('POST',
            $url,
            [
                'activity_date' => [
                    '2020-01-01',
                    'abc123',
                ]
            ]
        );

        $response->assertStatus(422)
            ->assertSee("All activity dates must be a valid date format.");
    }

    public function testItCreatesASetOfTaskDates()
    {
        $tasks = $this->seedTaskDates();
        $testTask = $tasks->first();
        $dates = [
            Carbon::today()->toDateString(),
            Carbon::today()->subDay(1)->toDateString(),
        ];

        $url = $this->apiPrefix . 'tasks/' . $testTask->id . '/dates';
        $response = $this->json('POST',
            $url, ['activity_date' => $dates,]
        );

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'task_id',
                        'activity_date',
                        'recorded_date',
                    ]
                ]
            ]);
        foreach ($dates as $date) {
            $response->assertJsonFragment([
                'task_id' => (int)$testTask->id,
                'activity_date' => $date,
                'recorded_date' => Carbon::today()->toDateString(),
            ]);

            $this->assertDatabaseHas('task_dates', [
                'task_id' => (int)$testTask->id,
                'activity_date' => $date. " 00:00:00",
            ]);
        }
    }

    public function testItDoesNotCreateDuplicateDates()
    {
        $testTask = Task::factory()->create();
        $dates = [
            Carbon::today()->toDateString(),
            Carbon::today()->subDay(1)->toDateString(),
            Carbon::today()->subDay(2)->toDateString(),
            Carbon::today()->subDay(1)->toDateString(),
        ];

        $url = $this->apiPrefix . 'tasks/' . $testTask->id . '/dates';
        $response = $this->json('POST',
            $url, ['activity_date' => $dates,]
        );

        $this->assertCount(3, $testTask->taskDates()->get());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'task_id',
                        'activity_date',
                        'recorded_date',
                    ]
                ]
            ]);
        foreach ($dates as $date) {
            $response->assertJsonFragment([
                'task_id' => (int)$testTask->id,
                'activity_date' => $date,
                'recorded_date' => Carbon::today()->toDateString(),
            ]);

            $this->assertDatabaseHas('task_dates', [
                'task_id' => (int)$testTask->id,
                'activity_date' => $date. " 00:00:00",
            ]);
        }
    }

    public function testItDoesNotDeleteExistingDates()
    {
        $testTask = Task::factory()->create();
        $allDates = [];
        for ($d = 20; $d < 23; ++$d) {
            $testTask->taskDates()->create([
                'activity_date' => Carbon::today()->subDay($d)
            ]);
            $allDates[] = Carbon::today()->subDay($d)->toDateString();
        }
        $newDates = [
            Carbon::today()->toDateString(),
            Carbon::today()->subDay(1)->toDateString(),
            Carbon::today()->subDay(2)->toDateString(),
        ];
        $allDates = array_merge($allDates, $newDates);

        $url = $this->apiPrefix . 'tasks/' . $testTask->id . '/dates';
        $response = $this->json('POST',
            $url, ['activity_date' => $newDates,]
        );

        $this->assertCount(6, $testTask->taskDates()->get());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'task_id',
                        'activity_date',
                        'recorded_date',
                    ]
                ]
            ]);
        foreach ($allDates as $date) {
            $response->assertJsonFragment([
                'task_id' => (int)$testTask->id,
                'activity_date' => $date,
            ]);

            $this->assertDatabaseHas('task_dates', [
                'task_id' => (int)$testTask->id,
                'activity_date' => $date. " 00:00:00",
            ]);
        }
    }

    public function testItDoesDeleteExistingDatesIfRequested()
    {
        $testTask = Task::factory()->create();
        $oldDates = [];
        for ($d = 20; $d < 23; ++$d) {
            $testTask->taskDates()->create([
                'activity_date' => Carbon::today()->subDay($d)
            ]);
            $oldDates[] = Carbon::today()->subDay($d)->toDateString();
        }
        $newDates = [
            Carbon::today()->toDateString(),
            Carbon::today()->subDay(1)->toDateString(),
            Carbon::today()->subDay(2)->toDateString(),
        ];

        $url = $this->apiPrefix . 'tasks/' . $testTask->id . '/dates';
        $response = $this->json('POST',
            $url, ['activity_date' => $newDates, 'replace' => true]
        );

        $this->assertCount(3, $testTask->taskDates()->get());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'task_id',
                        'activity_date',
                        'recorded_date',
                    ]
                ]
            ]);
        foreach ($newDates as $date) {
            $response->assertJsonFragment([
                'task_id' => (int)$testTask->id,
                'activity_date' => $date,
            ]);

            $this->assertDatabaseHas('task_dates', [
                'task_id' => (int)$testTask->id,
                'activity_date' => $date. " 00:00:00",
            ]);
        }
        foreach ($oldDates as $date) {
            $response->assertDontSee($date);

            $this->assertDatabaseMissing('task_dates', [
                'task_id' => (int)$testTask->id,
                'activity_date' => $date. " 00:00:00",
            ]);
        }
    }

    public function testItDeletesExistingDatesIfRequested()
    {
        $testTask = Task::factory()->create();
        $oldDates = [];
        for ($d = 20; $d < 23; ++$d) {
            $testTask->taskDates()->create([
                'activity_date' => Carbon::today()->subDay($d)
            ]);
            $oldDates[] = Carbon::today()->subDay($d)->toDateString();
        }
        $newDates = [];

        $url = $this->apiPrefix . 'tasks/' . $testTask->id . '/dates';
        $response = $this->json('POST',
            $url, ['activity_date' => $newDates, 'replace' => true]
        );

        $this->assertCount(0, $testTask->taskDates()->get());

        $response->assertStatus(200);

        foreach ($oldDates as $date) {
            $response->assertDontSee($date);

            $this->assertDatabaseMissing('task_dates', [
                'task_id' => (int)$testTask->id,
                'activity_date' => $date. " 00:00:00",
            ]);
        }
    }


    private function seedTaskDates()
    {
        return Task::factory()
            ->has(TaskDate::factory()->count(3), 'taskDates')
            ->count(4)
            ->create();
    }
}
