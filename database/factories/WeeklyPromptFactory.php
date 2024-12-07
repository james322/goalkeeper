<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WeeklyPrompt>
 */
class WeeklyPromptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user' => $this->faker->sentence(),
            'assistant' => $this->faker->sentence(),
            'personality' => 'polite',
        ];
    }
}
