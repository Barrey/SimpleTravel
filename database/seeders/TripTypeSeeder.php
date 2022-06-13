<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TripType;

class TripTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'type' => 'Business',
                'status' => true
            ],
            [
                'type' => 'Personal',
                'status' => true
            ]
        ];

        foreach ($data as $input) {
            TripType::create($input);
        }    
        
    }
}
