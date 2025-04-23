<?php

namespace Database\Factories;

use App\Models\Problem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProblemFactory extends Factory
{
    protected $model = Problem::class;

    public function definition()
    {
        return [
            'problem_number' => $this->faker->unique()->numberBetween(1000, 9999),
            'caller_id' => function () {
                return \App\Models\Caller::factory()->create()->caller_id;
            },
            'operator_id' => function () {
                return \App\Models\User::factory()->create(['role' => 'operator'])->id;
            },
            'problem_type_id' => function () {
                return \App\Models\ProblemType::factory()->create()->problem_type_id;
            },
            'equipment_serial' => function () {
                return \App\Models\Equipment::factory()->create()->serial_number;
            },
            'status' => $this->faker->randomElement(['open', 'assigned', 'resolved', 'unsolvable']),
            'reported_time' => now(),
            'notes' => $this->faker->sentence,
            'unsolvable_reason' => fn($attributes) => $attributes['status'] === 'unsolvable' ? $this->faker->sentence : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}