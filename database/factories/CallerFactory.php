<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CallerFactory extends Factory
{
    protected $model = \App\Models\Caller::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'job_title' => $this->faker->jobTitle,
            'department' => $this->faker->randomElement(['Sales', 'IT', 'HR']),
        ];
    }
}