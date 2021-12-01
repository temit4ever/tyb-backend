<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskDate;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testItListsAllTasks()
    {
        $this->seedTasks();
        $response = $this->json('GET', $this->apiPrefix . 'tasks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'title',
                        'description',
                        'type_id',
                        'priority',
                        'blocked',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ]);
        Task::all()->each(function ($task) use ($response) {
            $response->assertJsonFragment([
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'type_id' => (int) $task->type_id,
                'priority' => (int) $task->priority,
                'blocked' => (int) $task->blocked,
            ]);
        });
    }

    public function testItIncludesDatesAndUsersIfRequested()
    {
        $this->seedTasks();
        $response = $this->json('GET', $this->apiPrefix . 'tasks?include=task-dates,users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'title',
                        'description',
                        'type_id',
                        'priority',
                        'blocked',
                        'created_at',
                        'updated_at',
                        'dates',
                        'users'
                    ]
                ]
            ]);
        Task::all()->each(function ($task) use ($response) {
            $task->taskDates->each(function ($taskDate) use ($response, $task) {
                $response->assertJsonFragment([
                    'task_id' => $task->id,
                    'activity_date' => $taskDate->activity_date->toDateString(),
                    'recorded_date' => $task->created_at->toDateString(),
                ]);
            });
            $task->users->each(function ($user) use ($response, $task) {
                $response->assertJsonFragment([
                    'id' => $user->id,
                    'title' => $user->title,
                ]);
            });
        });
    }

    public function testItGetsOneTask()
    {
        $this->seedTasks();
        $response = $this->json('GET', $this->apiPrefix . 'tasks/2');

        $task = Task::find(2);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'type_id',
                    'priority',
                    'blocked',
                    'created_at',
                    'updated_at',
                ]
            ])
            ->assertJsonFragment([
                'title' => $task->title,
                'description' => $task->description,
                'type_id' => (int) $task->type_id,
                'priority' => (int) $task->priority,
                'blocked' => (int) $task->blocked,
            ]);
    }

    public function testItIncludesDatesAndUsersIfRequestedForOneTask()
    {
        $this->seedTasks();
        $response = $this->json('GET', $this->apiPrefix . 'tasks/2?include=task-dates,users');

        $task = Task::find(2);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'type_id',
                    'priority',
                    'blocked',
                    'created_at',
                    'updated_at',
                    'dates',
                    'users',
                ]
            ])
            ->assertJsonFragment([
                'title' => $task->title,
                'description' => $task->description,
                'type_id' => (int) $task->type_id,
                'priority' => (int) $task->priority,
                'blocked' => (int) $task->blocked,
            ]);
        $task->taskDates->each(function ($taskDate) use ($response, $task) {
            $response->assertJsonFragment([
                'task_id' => $task->id,
                'activity_date' => $taskDate->activity_date->toDateString(),
                'recorded_date' => $task->created_at->toDateString(),
            ]);
        });
        $task->users->each(function ($user) use ($response, $task) {
            $response->assertJsonFragment([
                'id' => $user->id,
                'title' => $user->title,
            ]);
        });
    }

    public function testItDeletesOneTask()
    {
        $this->seedTasks();
        $response = $this->json('DELETE', $this->apiPrefix . 'tasks/2');

        $response->assertStatus(200);
        $this->assertSoftDeletedInDatabase('tasks', ['id' => 2]);
        $this->assertNotSoftDeletedInDatabase('tasks', ['id' => 1]);
        $this->assertNotSoftDeletedInDatabase('tasks', ['id' => 3]);
        $this->assertDatabaseMissing('task_dates', ['task_id' => 2]);
    }

    public function testItValidatesTheRequestWhenCreating()
    {
        $response = $this->json('POST',
            $this->apiPrefix . 'tasks',
            []
        );
//        dd(json_decode($response->getContent()));

        $response->assertStatus(422)
            ->assertSee('The title field is required.')
            ->assertSee('The type slug field is required.')
            ->assertSee('The priority field is required.')
            ->assertSee('The blocked field is required.');
    }

    public function testItValidatesTheUserIdsWhenCreating()
    {
        $taskType = TaskType::factory()->create();
        $response = $this->json('POST',
            $this->apiPrefix . 'tasks',
            [
                'title' => 'Test Created 1',
                'description' => 'My sample description',
                'user_ids' => ['abc', 9999999],
                'type_slug' => $taskType->slug,
                'priority' =>3,
                'blocked' => 0,
            ]
        );
//        dd(json_decode($response->getContent()));

        $response->assertStatus(422)
            ->assertSee('The user_ids.0 must be an integer.')
            ->assertSee('The selected user_ids.1 is invalid.');
    }

    public function testItCreatesATask()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $taskType = TaskType::factory()->create();

        $data = [
            'title' => 'Test Created 1',
            'description' => 'My sample description',
            'user_ids' => [$user1->id, $user2->id],
            'type_slug' => $taskType->slug,
            'priority' =>3,
            'blocked' => 0,
        ];
        $response = $this->json('POST', $this->apiPrefix . 'tasks', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'type_id',
                    'priority',
                    'blocked',
                    'created_at',
                    'updated_at',
                ]
            ])
            ->assertJsonFragment([
                'title' => 'Test Created 1',
                'description' => 'My sample description',
                'type_id' => $taskType->id,
                'priority' =>3,
                'blocked' => 0,
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => $data['title'],
            'type_id' => $taskType->id,
            'description' => $data['description'],
            'priority' => $data['priority'],
            'blocked' => $data['blocked'],
        ]);

        $response_json = json_decode($response->getContent(), true);
        $task_id = $response_json['data']['id'];

        $this->assertDatabaseHas('task_user', [
            'task_id' => $task_id,
            'user_id' => $user1->id,
        ]);

        $this->assertDatabaseHas('task_user', [
            'task_id' => $task_id,
            'user_id' => $user2->id,
        ]);
    }

    public function testItValidatesTheRequestWhenUpdating()
    {
        $this->seedTasks();
        $response = $this->json('PUT',
            $this->apiPrefix . 'tasks/2',
            []
        );

        $response->assertStatus(422)
            ->assertSee('The title field is required.')
            ->assertSee('The type slug field is required.')
            ->assertSee('The priority field is required.')
            ->assertSee('The blocked field is required.');
    }

    public function testItValidatesTheUserIdsWhenUpdating()
    {
        $this->seedTasks();
        $response = $this->json('PUT',
            $this->apiPrefix . 'tasks/2',
            [
                'user_ids' => ['abc', 9999999],
            ]
        );
//        dd(json_decode($response->getContent()));

        $response->assertStatus(422)
            ->assertSee('The user_ids.0 must be an integer.')
            ->assertSee('The selected user_ids.1 is invalid.');
    }


    public function testItUpdatesATask()
    {
        $this->seedTasks();
        $existing_users = User::all();
        $taskType = TaskType::factory()->create();

        $data = [
            'title' => 'Test Updated 1',
            'description' => 'My sample description',
            'type_slug' => $taskType->slug,
            'priority' =>3,
            'blocked' => 0,
        ];
        $response = $this->json('PUT', $this->apiPrefix . 'tasks/3', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'type_id',
                    'priority',
                    'blocked',
                    'created_at',
                    'updated_at',
                ]
            ])
            ->assertJsonFragment([
                'title' => 'Test Updated 1',
                'description' => 'My sample description',
                'type_id' => $taskType->id,
                'priority' =>3,
                'blocked' => 0,
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => 3,
            'title' => $data['title'],
            'type_id' => $taskType->id,
            'description' => $data['description'],
            'priority' => $data['priority'],
            'blocked' => $data['blocked'],
        ]);
        foreach ($existing_users as $existing_user) {
            $this->assertDatabaseHas('task_user', [
                'task_id' => 3,
                'user_id' => $existing_user->id,
            ]);
        }
    }


    public function testItReplacesTaskUsersIfProvided()
    {
        $this->seedTasks();
        $existing_users = User::all();
        $new_user = User::factory()->create();
        $taskType = TaskType::factory()->create();

        $data = [
            'title' => 'Test Updated 1',
            'description' => 'My sample description',
            'type_slug' => $taskType->slug,
            'priority' =>3,
            'blocked' => 0,
            'user_ids' => [$new_user->id],
        ];
        $response = $this->json('PUT', $this->apiPrefix . 'tasks/3', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'type_id',
                    'priority',
                    'blocked',
                    'created_at',
                    'updated_at',
                ]
            ])
            ->assertJsonFragment([
                'title' => 'Test Updated 1',
                'description' => 'My sample description',
                'type_id' => $taskType->id,
                'priority' =>3,
                'blocked' => 0,
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => 3,
            'title' => $data['title'],
            'type_id' => $taskType->id,
            'description' => $data['description'],
            'priority' => $data['priority'],
            'blocked' => $data['blocked'],
        ]);
        foreach ($existing_users as $existing_user) {
            $this->assertDatabaseMissing('task_user', [
                'task_id' => 3,
                'user_id' => $existing_user->id,
            ]);
        }
        $this->assertDatabaseHas('task_user', [
            'task_id' => 3,
            'user_id' => $new_user->id,
        ]);
    }


    private function seedTasks()
    {
        $users = User::factory()->count(3)->create();
        Task::factory()
            ->count(4)
            ->hasAttached($users)
            ->has(TaskDate::factory()->count(3))
            ->create();
    }
}
