<?php

namespace Database\Factories;

use App\Models\JenisCuti;
use Illuminate\Database\Eloquent\Factories\Factory;

class JenisCutiFactory extends Factory
{
    protected $model = JenisCuti::class;

    public function definition(): array
    {
        $types = [
            'Cuti Tahunan',
            'Cuti Sakit',
            'Cuti Melahirkan',
            'Cuti Menikah',
            'Cuti Khusus'
        ];

        return [
            'cuti' => $this->faker->randomElement($types),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
