<?php

namespace App\Interfaces;

interface CityInterface
{
    /**
     * Create New Item
     *
     * @return object Created City
     */
    public function create(array $data);

    /**
     * Get Weather Data
     *
     * @param string City Name
     * @return array Weather Data
     */
    public function fetchWeather(string $cityName);
}
