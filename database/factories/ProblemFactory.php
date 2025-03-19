<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProblemFactory extends Factory
{
    protected $model = \App\Models\Problem::class;

    public function definition()
    {
        return [
            'caller_id' => \App\Models\Caller::factory(),
            'operator_id' => \App\Models\User::factory()->state(['role' => 'operator']),
            'problem_type_id' => \App\Models\ProblemType::factory(),
            'equipment_serial' => \App\Models\Equipment::factory(),
            'software_id' => null,
            'status' => 'open',
            'reported_time' => now(),
            'notes' => $this->faker->sentence,
        ];
    }
}