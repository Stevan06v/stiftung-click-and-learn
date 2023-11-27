<?php

namespace Database\Factories;

use App\Models\AmsRgs;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AmsAdvisor>
 */
class AmsAdvisorFactory extends Factory
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
            'email' => $this->faker->email,
            'ams_rgs_id' => AmsRgs::factory(),
            'function' => $this->faker->numberBetween(1, 5),
            'department_head' => $this->faker->boolean,
            'phone_number' => $this->faker->phoneNumber,
            'note' => $this->faker->text,
            'created_at' => $this->faker->dateTime
        ];
    }
}
