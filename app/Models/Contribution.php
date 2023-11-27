<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contribution extends Model
{
    use HasFactory;

	protected $fillable = [
		"participant_id",
		"year",
		"month",
		"attendance_list_received",
		"company_basic_contribution",
		"basic_scholarship",
		"additional_scholarship",
		"foundation_management",
		"course_cost"
	];

	public function participant(): BelongsTo
	{
		return $this->belongsTo(Participant::class);
	}
}
