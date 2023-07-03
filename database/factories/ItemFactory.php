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
            'unit_id' => $this->faker->numberBetween(1,8),
            'brand_id' => $this->faker->numberBetween(1,3),
            'beginning_stock' => $this->faker->numberBetween(50,100),
            'warehouse_id' =>1,
            'created_by' => 1,
        ];
    }
}
