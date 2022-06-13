<?php

namespace Database\Seeders;

use App\Models\TripType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $seeders = [
            UserSeeder::class,
            CountrySeeder::class,
            TripTypeSeeder::class
        ];

        foreach ($seeders as $seeder) {
            $this->call($seeder);
        }
    }
}
