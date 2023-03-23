<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
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
            'branch_code' => $this->faker->randomDigit(),
            'address' => $this->faker->address(),
            'phone_number' => $this->faker->phoneNumber(),
            'head_id' => $this->faker->numberBetween(1, 3),
            'email' => $this->faker->safeEmail(),
            'fax_number' => $this->faker->phoneNumber(),
            'created_by' => 1,
        ];
    }
}
