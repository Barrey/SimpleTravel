<?php

namespace Tests\Feature;

use Database\Seeders\CountrySeeder;
use Database\Seeders\TripTypeSeeder;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Testing\Fluent\AssertableJson;

class TripTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
        $this->seed([
            CountrySeeder::class,
            TripTypeSeeder::class
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_trip()
    {
        $user = \App\Models\User::factory()->create();

        Sanctum::actingAs($user);

        $city = \App\Models\City::limit(2)->get();

        $response = $this->post('/api/trip/create', [
            'user_id' => $user->id,
            'title' => 'test',
            'origin_city_id' => $city[0]->id,
            'destination_city_id' => $city[1]->id,
            'date_start' => '2023-11-01 10:00:00',
            'date_end' => '2023-12-01 18:00:00',
            'trip_type' => 1,
            'description' => 'test write description'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('user_trips', [
            'user_id' => $user->id
        ]);
    }

    public function test_create_trip_with_some_field_missing()
    {
        $user = \App\Models\User::factory()->create();

        Sanctum::actingAs($user);

        $city = \App\Models\City::limit(2)->get();
        
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/trip/create', [
            'user_id' => $user->id,
            'title' => '',
            'origin_city_id' => $city[0]->id,
            'destination_city_id' => $city[1]->id,
            'date_start' => '2023-11-01 10:00:00',
            'date_end' => '2023-12-01 18:00:00',
            'trip_type' => 1,
            'description' => 'test write description'
        ]);

        $response->assertJson(function (AssertableJson $json){
            $json->has('message')
                ->has('errors');
        });
    }

    public function test_update_trip()
    {
        $data = $this->createDataTrip();

        $response = $this->put('/api/trip/update/' . $data['user_trip']->id, [
            'user_id' => $data['user']->id,
            'title' => 'Trip change plan !!!',
            'date_start' => '2023-11-02 10:00:00',
            'trip_type' => 1,
        ])->assertJson([
            "message" => "Data trip updated"
        ]);
    }

    public function test_update_trip_with_other_user_data()
    {
        $user = \App\Models\User::factory()->create();

        Sanctum::actingAs($user);
        $city = \App\Models\City::limit(2)->get();

        $data = [
            'user_id' => 20,
            'title' => 'Trip to Bali',
            'origin' => $city[0]->id,
            'destination' => $city[1]->id,
            'start_trip' => '2022-12-01 10:00:00',
            'end_trip' => '2021-12-12 10:10:00',
            'trip_type_id' => 1,
            'description' => 'Planning to Bali with family'
        ];

        $user_trip = \App\Models\UserTrip::create($data);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->put('/api/trip/update/' . $user_trip->id, [
            'user_id' => $user->id,
            'title' => 'Trip change plan !!!',
            'date_start' => '2023-11-02 10:00:00',
            'trip_type' => 1,
        ])->assertJson([
            "message" => "Data not found"
        ]);
    }

    public function test_delete_trip()
    {
        $data = $this->createDataTrip();
        $this->delete('/api/trip/delete/'.$data['user_trip']->id)->assertJson([
            "message" => "Data trip deleted"
        ]);
    }

    public function test_delete_trip_dont_exist()
    {
        $data = $this->createDataTrip();
        $this->delete('/api/trip/delete/213')->assertJson([
            "message" => "Data trip not found"
        ]);
    }

    public function test_get_trip_detail()
    {
        $data = $this->createDataTrip();
        $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/api/trip/get/'.$data['user_trip']->id)->assertStatus(200);
    }

    public function test_get_trip_detail_dont_exist()
    {
        $data = $this->createDataTrip();
        $this->get('/api/trip/get/213')->assertJson([
            "message" => "Your Data trip not found"
        ]);
    }

    public function test_list_trip()
    {
        $data = $this->createDataTrip();
        $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/api/trip/list')->assertJson(function (AssertableJson $json){
            $json->has('0.city_origin.id');
            $json->has('0.city_departure.id');
            $json->has('0.trip_type.id');
            $json->has('0.user_id');
        });
    }

    private function createDataTrip(): array
    {
        $user = \App\Models\User::factory()->create();

        Sanctum::actingAs($user);
        $city = \App\Models\City::limit(2)->get();

        $data = [
            'user_id' => $user->id,
            'title' => 'Trip to Bali',
            'origin' => $city[0]->id,
            'destination' => $city[1]->id,
            'start_trip' => '2022-12-01 10:00:00',
            'end_trip' => '2021-12-12 10:10:00',
            'trip_type_id' => 1,
            'description' => 'Planning to Bali with family'
        ];

        $user_trip = \App\Models\UserTrip::create($data);

        return ['user' => $user, 'user_trip' => $user_trip];
    }
}
