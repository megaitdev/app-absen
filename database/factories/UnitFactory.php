<?php

namespace Database\Factories;

use App\Models\mak_hrd\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    protected $model = Unit::class;

    public function definition(): array
    {
        return [
            'nama' => $this->faker->company . ' Department',
            'kode' => $this->faker->unique()->regexify('[A-Z]{3}'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
