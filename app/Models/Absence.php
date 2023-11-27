<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absence extends Model
{
    use HasFactory;

	protected $fillable = [
		"participant_id",
		"start_date",
		"end_date",
		"type",
		"business_days",
		"annotation"
	];

	public function participant(): BelongsTo
	{
		return $this->belongsTo(Participant::class);
	}

}
