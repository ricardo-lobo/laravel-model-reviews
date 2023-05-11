<?php

namespace RicardoLobo\LaravelModelReviews\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use RicardoLobo\LaravelModelReviews\Models\Question;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'active' => $this->faker->boolean,
        ];
    }

    public function active(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'active' => true,
            ];
        });
    }

    public function inactive(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'active' => false,
            ];
        });
    }
}
