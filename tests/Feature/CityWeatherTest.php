<?php
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\City;

uses(WithFaker::class,RefreshDatabase::class);

it('fetch city weather list', function () {
    $this->artisan('db:seed');
    $response = $this->get("/api/v1/fetch-weather");
    $response->assertStatus(200);
});

it('fetch city weather list for the city having data', function () {
    $this->artisan('db:seed');
    $response = $this->get("/api/v1/fetch-weather/Telang");
    $response->assertStatus(200);
});

it('fetch city weather list for the city with blank data', function () {
    $data = City::factory()->create();
    $response = $this->get("/api/v1/fetch-weather/" .$data['name']);
    $response->assertStatus(404);
});