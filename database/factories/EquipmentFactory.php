<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EquipmentFactory extends Factory
{
    protected $model = \App\Models\Equipment::class;

    public function definition()
    {
        return [
            'serial_number' => $this->faker->unique()->bothify('EQ###'),
            'type' => $this->faker->randomElement(['Laptop', 'Printer', 'Desktop']),
            'make' => $this->faker->randomElement(['Dell', 'HP', 'Lenovo']),
        ];
    }
}