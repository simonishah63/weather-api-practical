<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = ['Ahmedabad', 'Surat', 'Telang', 'Agra', 'Raypur'];
        foreach ($items as $item) {
            City::firstOrCreate(['name' => $item]);
        }

    }
}
