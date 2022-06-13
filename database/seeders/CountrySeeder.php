<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\City;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Country::factory()->create(
            ['name' => 'Indonesia'])
            ->each(function ($country) {
                City::factory(20)->create(
                    ['country_id' => $country->id]
                );
            });
    }
}
