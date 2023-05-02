<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class, RefreshDatabase::class);

beforeEach(function () {
    $this->requestArr = [
        'name' => $this->faker->name,
    ];
    $this->headerOptions = [
        'Accept' => 'application/json',
    ];
});

it('Test City Name is Required', function () {
    $response = $this->postJson('/api/v1/city', [], $this->headerOptions);
    $response->assertStatus(422)
        ->assertJson([
            'message' => [
                'name' => [
                    'The name field is required.',
                ],
            ],
        ]);
});

it('Test City Unique Name Validation', function () {
    $this->artisan('db:seed');
    $response = $this->postJson('/api/v1/city', ['name' => 'Telang'], $this->headerOptions);
    $response->assertStatus(422)
        ->assertJson([
            'message' => [
                'name' => [
                    'The name has already been taken.',
                ],
            ],
        ]);
});

it('Test City Created Successfully', function () {
    $this->artisan('db:seed');
    $response = $this->postJson('/api/v1/city', $this->requestArr, $this->headerOptions);
    $response->assertStatus(200)
        ->assertJsonStructure(
            [
                'data' => [
                    'id',
                    'uuid',
                    'name',
                    'created_at',
                    'updated_at',
                ],
                'message',
                'status',
                'errors',
            ]
        );

});
