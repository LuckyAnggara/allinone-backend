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
            'sku' => $this->faker->ean13(),
            'category_id' => $this->faker->numberBetween(1, 3),
            'brand' => $this->faker->word(),
            'unit_id' => $this->faker->numberBetween(1, 8),
            'description' => $this->faker->realText($maxNbChars = 200, $indexSize = 2),
            'selling_price' => $this->faker->randomNumber(5, true),
            'buying_price' => $this->faker->randomNumber(4, true),
            'selling_tax_id' => 1,
            'buying_tax_id' => 1,
            'warehouse_id' => 1,
            'created_by' => 1,
            'branch_id' => 1,
        ];
    }
}
