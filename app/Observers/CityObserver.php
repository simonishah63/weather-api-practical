<?php

namespace App\Observers;

use App\Helpers\WeatherHelper;
use App\Models\City;

class CityObserver
{
    /** @var App\Helpers\WeatherHelper */
    private $weatherHelper;

    public function __construct(WeatherHelper $weatherhelper)
    {
        $this->weatherhelper = $weatherhelper;
    }

    /**
     * Handle the City "created" event.
     */
    public function created(City $city): void
    {
        $response = $this->weatherhelper->fetchWeather($city->name);
    }
}
