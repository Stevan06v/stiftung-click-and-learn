<?php

namespace Database\Factories;

use App\Models\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
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
			'designation' => $this->faker->text,
			'training_provider' => $this->faker->text,
			'start_date' =>  $this->faker->date,
			'end_date' =>  $this->faker->date,
			'date' =>  $this->faker->date,
			'invoice_number' => $this->faker->text,
			'amount' => $this->faker->randomNumber(),
			'certificate' => $this->faker->boolean,
			'referral' => $this->faker->randomFloat(),
        ];
    }
}
