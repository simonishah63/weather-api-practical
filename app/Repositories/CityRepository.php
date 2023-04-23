<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Models\City;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;

class CityRepository
{
    /**
     * Create New City.
     *
     * @param array $data
     * @return object City Object
     */
    public function create(array $data): City
    {
        return City::create($data);
    }


    /**
     * Fetch weather info for city
     *
     * @param object $city
     * @return array $responseArr
     */
    public function fetchWeather($cityName = '')
    {
        if($cityName) {
            $city = City::where('name', ucfirst($cityName))->get();
        } else {
            $city = City::get();
        }

        return ($city->isNotEmpty()) ? $this->transformWeatherData($city) : [];
    }

    /**
     *  
     * Generate Date range
     */
    public function generateDateRange() : array
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
     * 
     * Format Weather Info  
     * @return array $weatherInfo
     */
    private function transformWeatherData($city) : Array
    {
        $responseArr = [];
        
        $dates = $this->generateDateRange();
    
        $redis = Redis::connection();
        foreach($city as $cityValue) {
            $weatherData = $redis->get($cityValue['name']);
            if(!empty($weatherData)) {
                $responseArr[] = json_decode($weatherData);
                continue;
            }
            $weatherData = json_decode($cityValue['weather_data'], true);
            if(empty($weatherData['list'])) {
                continue;
            }
            $weatherDataArr = [];
            $weatherDataArr['name'] = $weatherData['city']['name'];
            $weatherDataArr['coord'] = $weatherData['city']['coord'];  
            $weatherArr = [];
            foreach($weatherData['list'] as $data) {
                $checkDate = date('Y-m-d', $data['dt']);
                if(!in_array($checkDate, $dates)) {
                    continue;
                }
                $from = Carbon::createFromDate($checkDate);
                $to = Carbon::now();
                $day = 'Day-'. $from->diffInDays($to);
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