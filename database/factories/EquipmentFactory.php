<?php

namespace Database\Factories;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;

class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    public function definition()
    {
        return [
            'serial_number' => $this->faker->unique()->regexify('[A-Z0-9]{10}'),
            'type' => $this->faker->randomElement(['Laptop', 'Monitor', 'Printer']),
            'make' => $this->faker->company,
            'status' => $this->faker->randomElement(['active', 'inactive', 'under_repair']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}