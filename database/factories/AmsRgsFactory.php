<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AmsRgs>
 */
class AmsRgsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'street' => $this->faker->streetName,
            'postcode' => $this->faker->postcode,
            'city' => $this->faker->city,
            'email' => $this->faker->email,
            'phone_number' => $this->faker->phoneNumber,
            'note' => $this->faker->text,
            'created_at' => $this->faker->dateTime,
        ];
    }
}
