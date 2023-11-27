<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class NoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
			'title' => $this->faker->title,
			'text' => $this->faker->text,
			'participant_id' => Participant::factory(),
			'note_date' => $this->faker->dateTime,
			'user_id' => User::factory(),
        ];
    }
}
