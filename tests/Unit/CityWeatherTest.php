<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Repositories\CityRepository;
use App\Helpers\WeatherHelper;
use App\Models\City;
use Carbon\Carbon;

uses(Tests\TestCase::class, RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->cityRepository = new CityRepository();
    $this->weatherHelper = new WeatherHelper();
});

it('test Helper function for fetch weather information', function () {
    $this->artisan('migrate');
    $this->artisan('db:seed');
    $data = City::factory()->create();
    $response = $this->weatherHelper->fetchWeather($data["name"]);
    expect($response)->toBeBool();
});


it('test Repository function for fetch weather information for a particular city', function () {
    $request = new \Illuminate\Http\Request();
    $request->request->add(['city' => $this->faker->name]);
    $response = $this->cityRepository->fetchWeather($request->city);
    expect($response)->toBeArray();
});

it('test Repository function for fetch weather information', function () {
    $request = new \Illuminate\Http\Request();
    $response = $this->cityRepository->fetchWeather();
    expect($response)->toBeArray();
});

it('test Repository function for date range array', function () {
    $response = $this->cityRepository->generateDateRange();
    expect($response)->toBeArray();
});