<?php

namespace App\Repositories;

use App\Models\City;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Redis;

class CityRepository
{
    /**
     * Create New City.
     *
     * @return object City Object
     */
    public function create(array $data): City
    {
        return City::create($data);
    }

    /**
     * Fetch weather info for city
     *
     * @param  object  $city
     * @return array $responseArr
     */
    public function fetchWeather($cityName = '')
    {
        if ($cityName) {
            $city = City::where('name', ucfirst($cityName))->get();
        } else {
            $city = City::get();
        }

        return ($city->isNotEmpty()) ? $this->transformWeatherData($city) : [];
    }

    /**
     * Generate Date range
     */
    public function generateDateRange(): array
    {
        $period = CarbonPeriod::create(Carbon::now(), Carbon::now()->addDays(5));
        // Iterate over the period
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }

    /**
     * Format Weather Info
     *
     * @return array $weatherInfo
     */
    private function transformWeatherData($city): array
    {
        $responseArr = [];

        $dates = $this->generateDateRange();

        $redis = Redis::connection();
        foreach ($city as $cityValue) {
            //$redis->flushAll();
            if ($redis->exists($cityValue['name'])) {
                $weatherData = $redis->get($cityValue['name']);
                $responseArr[] = json_decode($weatherData);

                continue;
            }
            $weatherData = json_decode($cityValue['weather_data'], true);
            if (empty($weatherData['list'])) {
                continue;
            }
            $weatherDataArr = [];
            $weatherDataArr['name'] = $weatherData['city']['name'];
            $weatherDataArr['coord'] = $weatherData['city']['coord'];
            $weatherArr = [];
            foreach ($weatherData['list'] as $data) {
                $checkDate = date('Y-m-d', $data['dt']);
                if (! in_array($checkDate, $dates)) {
                    continue;
                }
                $from = Carbon::createFromDate($checkDate);
                $to = Carbon::now();
                $day = 'Day-'.$from->diffInDays($to);
                $weatherArr[$day]['date'] = $data['dt_txt'];
                $weatherArr[$day]['temp'] = $data['main']['temp'];
                $weatherArr[$day]['minimum_temp'] = $data['main']['temp_min'];
                $weatherArr[$day]['maximum_temp'] = $data['main']['temp_max'];
                $weatherArr[$day]['humidity'] = $data['main']['humidity'];
                $weatherArr[$day]['visibility'] = $data['visibility'];
                $weatherArr[$day]['wind_speed'] = $data['wind']['speed'];
            }
            $weatherDataArr['weather'] = $weatherArr;
            $redis->set($cityValue['name'], json_encode($weatherDataArr));
            $responseArr[] = $weatherDataArr;
        }

        return $responseArr;
    }
}
