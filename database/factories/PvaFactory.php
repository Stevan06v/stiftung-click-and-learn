<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pva>
 */
class PvaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'salutation' => $this->faker->numberBetween(0, 1),
            'title' => $this->faker->title,
            'firstname' => $this->faker->firstName,
            'lastname' => $this->faker->lastName,
            'phone_number' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'name' => $this->faker->name(),
            'street' => $this->faker->streetName,
            'postcode' => $this->faker->postcode,
            'city' => $this->faker->city,
            'region' => $this->faker->state,
            'note' => $this->faker->text(),
            'created_at' => $this->faker->unixTime,
        ];
    }
}
