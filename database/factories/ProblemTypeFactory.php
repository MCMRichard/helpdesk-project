<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProblemTypeFactory extends Factory
{
    protected $model = \App\Models\ProblemType::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'parent_type_id' => null,
        ];
    }
}