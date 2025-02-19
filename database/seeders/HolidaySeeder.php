<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Holiday;
use Faker\Factory as Faker;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $data = [];

        for ($i = 0; $i < 1000000; $i++) {
            $data[] = [
                'date' => $faker->date(),
                'note' => $faker->sentence(),
                'user_id' => 1, // Asumsi ID user antara 1 hingga 100
            ];

            // Insert data in chunks to optimize performance
            if ($i % 10000 == 0) {
                Holiday::insert($data);
                $data = []; // Clear data to avoid memory overload
            }
        }

        // Insert remaining data if any
        if (!empty($data)) {
            Holiday::insert($data);
        }
    }
}
