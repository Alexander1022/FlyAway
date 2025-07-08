<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\User;
use App\Models\Specie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
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
            'specie_id' => Specie::factory(),
            'lat' => fake()->latitude(),
            'lng' => fake()->longitude(),
        ];
    }
}
