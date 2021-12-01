<?php

namespace Tests\Feature;

use App\Models\Sprint;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SprintControllerTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testItListsAllSprints()
    {
        $this->seedSprints();
        $response = $this->json('GET', $this->apiPrefix . 'sprints');
//        dd(json_decode($response->getContent()));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'title',
                        'start_date',
                        'end_date'
                    ]
                ]
            ])
        ->assertJsonFragment(['title' => 'Sprint 1']);
    }

    public function testItGetsOneSprint()
    {
        $this->seedSprints();
        $response = $this->json('GET', $this->apiPrefix . 'sprints/2');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'start_date',
                    'end_date'
                ]
            ])
            ->assertJsonFragment(['title' => 'Sprint 2'])
            ->assertJsonMissing(['title' => 'Sprint 1'])
            ->assertJsonMissing(['title' => 'Sprint 3']);
    }

    public function testItDeletesOneSprint()
    {
        $this->seedSprints();
        $response = $this->json('DELETE', $this->apiPrefix . 'sprints/2');

        $response->assertStatus(200);
        $this->assertSoftDeletedInDatabase('sprints', ['id' => 2]);
        $this->assertNotSoftDeletedInDatabase('sprints', ['id' => 1]);
        $this->assertNotSoftDeletedInDatabase('sprints', ['id' => 3]);
    }

    public function testItValidatesTheRequestWhenCreating()
    {
        $response = $this->json('POST',
            $this->apiPrefix . 'sprints',
            []
        );

        $response->assertStatus(422)
            ->assertSee('The title field is required.')
            ->assertSee('The start date field is required.')
            ->assertSee('The end date field is required.');
    }

    public function testItRequiresTheEndDateToComeAfterTheStartDateWhenCreating()
    {
        $response = $this->json('POST',
            $this->apiPrefix . 'sprints',
            [
                'title' => 'Test 1',
                'start_date' => '2020-10-01',
                'end_date' => '2020-09-01',
            ]
        );

        $response->assertStatus(422)
            ->assertSee('The end date must be a date after start date.');
    }

    public function testItCreatesASprint()
    {
        $data = [
            'title' => 'Test Created 1',
            'start_date' => '2020-10-01T00:00:00.000000Z',
            'end_date' => '2020-11-01T00:00:00.000000Z',
        ];
        $response = $this->json('POST', $this->apiPrefix . 'sprints', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'start_date',
                    'end_date',
                    'created_at',
                    'updated_at',
                ]
            ])
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('sprints', ['title' => $data['title']]);
    }

    public function testItValidatesTheRequestWhenUpdating()
    {
        $this->seedSprints();
        $response = $this->json('PUT',
            $this->apiPrefix . 'sprints/2',
            []
        );

        $response->assertStatus(422)
            ->assertSee('The title field is required.')
            ->assertSee('The start date field is required.')
            ->assertSee('The end date field is required.');
    }

    public function testItRequiresTheTitleToHaveThreeCharactersWhenUpdating()
    {
        $this->seedSprints();
        $response = $this->json('PUT',
            $this->apiPrefix . 'sprints/2',
            ['title' => '12']
        );

        $response->assertStatus(422)
            ->assertSee('The title must be at least 3 characters.');
    }

    public function testItRequiresTheEndDateToComeAfterTheStartDateWhenUpdating()
    {
        $this->seedSprints();
        $response = $this->json('PUT',
            $this->apiPrefix . 'sprints/2',
            [
                'title' => 'Test 1',
                'start_date' => '2020-10-01',
                'end_date' => '2020-09-01',
            ]
        );

        $response->assertStatus(422)
            ->assertSee('The end date must be a date after start date.');
    }

    public function testItUpdatesASprint()
    {
        $this->seedSprints();
        $data = [
            'title' => 'Test Updated 1',
            'start_date' => '2020-10-01T00:00:00.000000Z',
            'end_date' => '2020-11-01T00:00:00.000000Z',
        ];
        $response = $this->json('PUT', $this->apiPrefix . 'sprints/3', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'start_date',
                    'end_date',
                    'created_at',
                    'updated_at',
                ]
            ])
            ->assertJsonFragment(['id' => 3])
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('sprints', ['id' => 3, 'title' => $data['title']]);
    }


    private function seedSprints()
    {
        for ($s = 1; $s < 4; ++$s) {
            $startDate = Carbon::createFromDate(2020, $s, 1);
            $endDate = clone $startDate;
            $endDate->addWeek(2);
            Sprint::factory()->create([
                'title' => 'Sprint ' . $s,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
        }
    }
}
