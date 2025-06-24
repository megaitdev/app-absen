<?php

namespace Database\Factories;

use App\Models\mak_hrd\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'nama' => $this->faker->name,
            'nip' => $this->faker->unique()->numerify('EMP####'),
            'pin' => $this->faker->unique()->numerify('####'),
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'is_deleted' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
