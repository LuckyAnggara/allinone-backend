<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'employee_id' => $this->faker->numberBetween(1, 5),
            'username' => $this->faker->userName(),
            'password' => '$2y$10$SKX.kBjyiXwCnnEo2jMsD.aCIuKhS52PeLqsQc21N7Ix3MRZ8rB6O', 
            'branch_id'=> 1, 
            'role_id'=> 4, 
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
