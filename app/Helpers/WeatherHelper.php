<?php

namespace App\Helpers;

use App\Models\City;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class WeatherHelper
{
    /**
     * @var apiUrl
     */
    private $apiUrl;

    /**
     * @var apiKey
     */
    private $apiKey;

    public function __construct()
    {
        $this->apiUrl = env('WEATHER_API_URL');
        $this->apiKey = env('WEATHER_API_KEY');
    }

    /**
     * Call weather API
     *
     * @param  string  $url
     * @param  array  $sendData
     * @return object
     */
    public function apiCall($url, $sendData)
    {
        try {
            $sendData = Arr::add($sendData, 'appid', $this->apiKey);
            $response = Http::get($this->apiUrl.$url, $sendData);

            if (in_array($response->status(), [200, 201])) {
                return $response->json();
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Fetch 5 days weather info for city
     *
     * @return object
     */
    public function fetchWeather($cityName)
    {
        $requestData = [
            'q' => $cityName,
        ];

        $weatherData = $this->apiCall('/data/2.5/forecast', $requestData);

        City::updateOrCreate(
            ['name' => $cityName],
            ['weather_data' => json_encode($weatherData)]
        );

        return true;
    }
}
