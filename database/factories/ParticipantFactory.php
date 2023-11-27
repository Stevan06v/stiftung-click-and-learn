<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\AmsAdvisor;
use App\Models\Company;
use App\Models\CompanyAdvisor;
use App\Models\EducationCategory;
use App\Models\Pva;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Participant>
 */
class ParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'matriculation_number' => $this->faker->numerify('##########'),
            'section' => $this->faker->numberBetween(0, 4),
            'salutation' => $this->faker->numberBetween(0, 1),
            'firstname' => $this->faker->firstName,
            'lastname' => $this->faker->lastName,
            'title' => $this->faker->title,
            'street' => $this->faker->streetName,
            'street_number' => $this->faker->buildingNumber,
            'stairs' => $this->faker->numberBetween(0, 50),
            'door' => $this->faker->numberBetween(0, 100),
            'city' => $this->faker->city,
            'postcode' => $this->faker->postcode,
            'phone_number' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'iban' => $this->faker->iban('AT'),
            'bic' => $this->faker->swiftBicNumber ,
            'svnr' => $this->faker->numerify('###-####/####'),
            'birthdate' => $this->faker->date(),
            'report' => $this->faker->boolean,
            'pva_id' => Pva::factory(),
            'activity_id' => Activity::factory(),
            'ams_advisor_id' => AmsAdvisor::factory(),
            'ams_status' =>  $this->faker->numberBetween(0, 2),
            'entry' => null,
            'exit' => null,
            'actual_exit' => null,
            'exit_reason' => null,
            'dv_date' => null,
            'career_goal' => null,
            'last_activity' => null,
            'pre_qualification' => null,
            'education_category_id' => EducationCategory::factory(),
            'education_form' =>  $this->faker->numberBetween(0, 4),
            'company_advisor_id' => CompanyAdvisor::factory(),
            'internship_location' => null,
            'vacation_entitlement' => null,
            'weekly_hours' => null,
            'entitlement_to_care_leave' => null,
            'coaching_date' => null,
            'aw_status' => null,
            'aw_status_date' => null,
            'education_plan' => $this->faker->boolean,
            'education_plan_approved' => $this->faker->boolean,
            'training_agreement' => $this->faker->boolean,
            'entry_notification_land' => $this->faker->boolean,
            'schALG_conversion' => $this->faker->boolean,
            'agreement_with_company' => $this->faker->boolean,
            'agreement_date' => null,
            'land_advance' => $this->faker->boolean,
            'land_final_bill' => $this->faker->boolean,
            'share_sign_land' => null,
            'education_cost_plan' => null,
            'subsidy_coursecost_charged' => $this->faker->boolean,
            'subsidy_coursecost_amount' => null,
            'land_request_ub' => null,
            'land_request_qb' => null,
            'land_request_educationcosts' => null,
            'land_request_date' => null,
            'land_request_approval_date' => null,
            'land_request_zlg_date' => null,
            'land_final_bill_amount' => null,
            'land_final_bill_request_date' => null,
            'land_final_bill_approval_date' => null,
            'land_final_bill_zlg_date' => null,
            'note' => $this->faker->text(),
            'created_at' => $this->faker->dateTime,
        ];
    }
}
