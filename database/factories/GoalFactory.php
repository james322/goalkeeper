<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Goal>
 */
class GoalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'intent' => $this->faker->sentence(),
            'is_complete' => $this->faker->boolean(),
        ];
    }

    public function incomplete(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_complete' => false,
            ];
        });
    }

    public function complete(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_complete' => true,
            ];
        });
    }
}
