<?php

namespace Database\Factories;

use App\Models\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contribution>
 */
class ContributionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
			'participant_id' => Participant::factory(),
			'year' => $this->faker->randomNumber(),
			'month' => $this->faker->randomNumber(),
			'attendance_list_received' => $this->faker->boolean,
			'company_basic_contribution' => $this->faker->randomNumber(),
			'basic_scholarship' => $this->faker->randomNumber(),
			'additional_scholarship' => $this->faker->randomNumber(),
			'foundation_management' => $this->faker->randomNumber(),
			'course_cost' => $this->faker->randomNumber(),
		];
    }
}
