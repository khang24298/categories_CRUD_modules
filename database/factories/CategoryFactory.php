<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
				$faker = Faker::create();
				$slug = $faker->slug();
      	return [
					'parent_id' => ($faker->numberBetween(1, 1000) % 2) ?? null, // Adjust as needed
					'level' => ($faker->randomDigit() % 2) + 1, // Adjust as needed
					'type' => 'service', // Adjust as needed
					'key' => $slug,
					'code' => strtoupper(substr($slug, 0, 3) . $faker->randomNumber(2)),
					'name' => json_encode(['en' => $faker->words(2, true)]),
					'active' => $faker->boolean(),
				];
    }
}
