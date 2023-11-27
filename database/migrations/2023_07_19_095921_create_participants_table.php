<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->string('matriculation_number')->nullable();
            $table->integer('section')->nullable();
            $table->integer('salutation')->nullable();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('title')->nullable();
            $table->string('street')->nullable();
            $table->string('street_number')->nullable();
            $table->string('stairs')->nullable();
            $table->string('door')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email');
            $table->string('iban')->nullable();
            $table->string('bic')->nullable();
            $table->string('svnr')->nullable();
            $table->date('birthdate');
            $table->boolean('report')->default(false);
			$table->unsignedBigInteger('pva_id')->nullable()->foreign('pva_id')->references('id')->on('pvas');                               // foreign key
            $table->unsignedBigInteger('activity_id')->nullable()->foreign('activity_id')->references('id')->on('activities');               // foreign key
            $table->unsignedBigInteger('ams_advisor_id')->nullable()->foreign('ams_advisor_id')->references('id')->on('ams_advisors');       // foreign key
            $table->integer('ams_status')->nullable();
            $table->date('entry')->nullable();
            $table->date('exit')->nullable();
            $table->date('actual_exit')->nullable();
            $table->string('exit_reason')->nullable();
            $table->date('dv_date')->nullable();
            $table->string('career_goal')->nullable();
            $table
				->string('last_activity')
				->nullable();
            $table
				->string('pre_qualification')
				->nullable();
            $table
				->unsignedBigInteger('education_category_id')
				->nullable()
				->foreign('education_category_id')
				->references('id')
				->on('education_categories');
			$table
				->integer('education_form')
				->nullable();
            $table->unsignedBigInteger('company_advisor_id')
				->nullable()
				->foreign('company_advisor_id')
				->references('id')
				->on('company_advisors');

			$table->unsignedBigInteger('user_id')->nullable();
			$table->foreign('user_id')->references('id')->on('users');

			$table
				->string('internship_location')
				->nullable();
            $table
				->string('vacation_entitlement')
				->nullable();
            $table->string('weekly_hours')->nullable();
            $table->string('entitlement_to_care_leave')->nullable();
            $table->date('coaching_date')->nullable();
            $table->integer('aw_status')->nullable();
            $table->date('aw_status_date')->nullable();
            $table->boolean('education_plan')->default(false);
            $table->boolean('education_plan_approved')->default(false);
            $table->boolean('training_agreement')->default(false);
            $table->boolean('entry_notification_land')->default(false);
			$table->boolean('is_complete')->default(false); // value
			$table->boolean('schALG_conversion')->default(false);
            $table->boolean('agreement_with_company')->default(false);
            $table->date('agreement_date')->nullable();
            $table->boolean('land_advance')->default(false);
            $table->boolean('land_final_bill')->default(false);
            $table->string('share_sign_land')->nullable();
            $table->string('education_cost_plan')->nullable();
            $table->boolean('subsidy_coursecost_charged')->default(false);
            $table->string('subsidy_coursecost_amount')->nullable();
            $table->string('land_request_ub')->nullable();
            $table->string('land_request_qb')->nullable();
            $table->string('land_request_educationcosts')->nullable();
            $table->date('land_request_date')->nullable();
            $table->date('land_request_approval_date')->nullable();
            $table->date('land_request_zlg_date')->nullable();
            $table->string('land_final_bill_amount')->nullable();
            $table->date('land_final_bill_request_date')->nullable();
            $table->date('land_final_bill_approval_date')->nullable();
            $table->date('land_final_bill_zlg_date')->nullable();
			$table->text('note')->nullable();
            $table->timestamps();
			$table->softDeletes();
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
