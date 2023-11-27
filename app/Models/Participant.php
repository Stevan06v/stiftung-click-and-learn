<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;


class Participant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'matriculation_number',
        'section',
        'salutation',
        'firstname',
        'lastname',
        'title',
        'street',
        'street_number',
        'stairs',
        'door',
        'city',
        'postcode',
        'phone_number',
        'email',
        'iban',
        'bic',
        'svnr',
        'birthdate',
        'report',
        'pva_id',
        'activity_id',
        'ams_advisor_id',
        'ams_status',
        'entry',
        'exit',
        'actual_exit',
        'exit_reason',
        'dv_date',
		'user_id',
        'career_goal',
        'last_activity',
        'pre_qualification',
        'education_category_id',
        'education_form',
        'company_advisor_id',
        'internship_location',
        'vacation_entitlement',
        'weekly_hours',
        'entitlement_to_care_leave',
        'coaching_date',
        'aw_status',
		'is_complete',
        'aw_status_date',
        'education_plan',
        'education_plan_approved',
        'training_agreement',
        'entry_notification_land',
        'schALG_conversion',
        'agreement_with_company',
        'agreement_date',
        'land_advance',
        'land_final_bill',
        'share_sign_land',
        'education_cost_plan',
        'subsidy_coursecost_charged',
        'subsidy_coursecost_amount',
        'land_request_ub',
        'land_request_qb',
        'land_request_educationcosts',
        'land_request_date',
        'land_request_approval_date',
        'land_request_zlg_date',
        'land_final_bill_amount',
        'land_final_bill_request_date',
        'land_final_bill_approval_date',
        'land_final_bill_zlg_date',
        'note'
    ];

	public function ams_advisor(): BelongsTo
	{
		return $this->belongsTo(AmsAdvisor::class);
	}

	public function company_advisor(): BelongsTo
	{
		return $this->belongsTo(CompanyAdvisor::class);
	}

	public function education_category(): BelongsTo{
		return $this->belongsTo(EducationCategory::class);
	}

	public function notes(): HasMany
	{
		return $this->hasMany(Note::class);
	}

	public function absences(): HasMany
	{
		return $this->hasMany(Absence::class);
	}

	public function documents(): HasMany
	{
		return $this->hasMany(Document::class);
	}

	public function contributions(): HasMany
	{
		return $this->hasMany(Contribution::class);
	}

	public function pva(): BelongsTo
	{
		return $this->belongsTo(Pva::class);
	}

	public function activity(): BelongsTo
	{
		return $this->belongsTo(Activity::class);
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

}
