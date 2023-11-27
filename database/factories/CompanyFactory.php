<?php

namespace Database\Factories;

use App\Models\CompanyAdvisor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'companyname1' => $this->faker->company,
            'companyname2' => $this->faker->companySuffix,
            'salutation' => $this->faker->numberBetween(0, 1),
            'street' => $this->faker->streetName,
            'postcode' => $this->faker->postcode,
            'city' => $this->faker->city,
            'phone_number' => $this->faker->phoneNumber,
            'fax' => $this->faker->phoneNumber,
            'phone_number_mobil' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'website' => $this->faker->url,
            'cooperation_agreement' => $this->faker->boolean,
            'note' => $this->faker->text,
            'hour_record' => $this->faker->text,
            'created_at' => $this->faker->dateTime,
        ];
    }
}
