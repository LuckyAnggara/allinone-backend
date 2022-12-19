<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
         return [
            'name' => $this->faker->word(),
            'unit_id' => $this->faker->randomDigit(),
            'brand_id' => $this->faker->randomDigit(),
            'warehouse_id' =>1,
            'created_by' => 1,
        ];
    }
}
