<?php

namespace Database\Factories;

use App\Models\Caller;
use Illuminate\Database\Eloquent\Factories\Factory;

class CallerFactory extends Factory
{
    protected $model = Caller::class;

    public function definition()
    {
        return [
            'caller_id' => $this->faker->unique()->numberBetween(1, 1000),
            'name' => $this->faker->name,
            'job_title' => $this->faker->jobTitle,
            'department' => $this->faker->randomElement(['Sales', 'IT', 'HR']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
