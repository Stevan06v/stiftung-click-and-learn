<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\Participant::factory(10)->create();
        \App\Models\Activity::factory(10)->create();
        \App\Models\AmsAdvisor::factory(10)->create();
        \App\Models\AmsRgs::factory(10)->create();
        \App\Models\Company::factory(10)->create();
        \App\Models\CompanyAdvisor::factory(10)->create();
        \App\Models\EducationCategory::factory(10)->create();
        \App\Models\Pva::factory(10)->create();
		\App\Models\Note::factory(10)->create();
		\App\Models\Absence::factory(10)->create();
		\App\Models\Contribution::factory(10)->create();
		\App\Models\Document::factory(10)->create();



		\App\Models\User::factory()->create([
            'name' => 'Julian Murach',
            'email' => 'test@clickandlearn.at',
			'password' => Hash::make('password')
        ]);
		\App\Models\User::factory()->create([
			'name' => 'Philipp Kiefer',
			'email' => 'pk@clickandlearn.at',
			'password' => Hash::make('password')
		]);
		\App\Models\User::factory()->create([
			'name' => 'Lorenz Wawra',
			'email' => 'lw@clickandlearn.at',
			'password' => Hash::make('password')
		]);
		\App\Models\User::factory()->create([
			'name' => 'Dominik Prinzensteiner',
			'email' => 'dp@clickandlearn.at',
			'password' => Hash::make('password')
		]);
    }
}
