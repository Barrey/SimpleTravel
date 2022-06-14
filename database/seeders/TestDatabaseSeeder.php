<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
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
