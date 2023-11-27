<?php

namespace Database\Factories;

use App\Models\Participant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Absence>
 */
class AbsenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
		$types = ['Urlaub', 'Krankenstand', 'Pflegefreistellung'];
        return [
			'participant_id' => Participant::factory(),
			'type' => $this->faker->randomElement($types),
			'start_date' =>  $this->faker->date,
			'end_date' =>  $this->faker->date,
			'business_days' =>  $this->faker->randomNumber(),
			'annotation' =>  $this->faker->text(),
		];
    }
}
