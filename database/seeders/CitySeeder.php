<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = ["Ahmedabad", "Surat", "Telang", "Agra", "Raypur"];
        foreach($items as $item) {
            City::firstOrCreate(['name' => $item]);
        }
        
    }
}
