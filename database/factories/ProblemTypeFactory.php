<?php

namespace Database\Factories;

use App\Models\ProblemType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProblemTypeFactory extends Factory
{
    protected $model = ProblemType::class;

    public function definition()
    {
        return [
            'problem_type_id' => $this->faker->unique()->numberBetween(1, 1000), // Optional, since it's auto-incrementing
            'name' => $this->faker->word, // Matches the 'name' column
            'parent_type_id' => null,     // Nullable, can be set later for hierarchy
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // Optional: State for hierarchical problem types
    public function withParent()
    {
        return $this->state(function (array $attributes) {
            return [
                'parent_type_id' => ProblemType::factory()->create()->problem_type_id,
            ];
        });
    }
}