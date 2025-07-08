<?php

namespace Database\Factories;

use App\Models\Specie;
use App\Models\Habitat;
use App\Models\User;
use App\Models\SpecieKingdom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Specie>
 */
class SpecieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'specie_kingdom_id' => SpecieKingdom::factory(),
            'habitat_id' => null, // Make it nullable as per your migration
            'common_name' => fake()->unique()->word(),
            'scientific_name' => fake()->unique()->word() . ' ' . fake()->word(),
        ];
    }

    /**
     * Indicate that the specie should have a habitat.
     */
    public function withHabitat(): static
    {
        return $this->state(fn (array $attributes) => [
            'habitat_id' => Habitat::factory(),
        ]);
    }
}
