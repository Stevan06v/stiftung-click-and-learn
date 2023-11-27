<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

	protected $fillable = [
		"participant_id",
		"designation",
		"training_provider",
		"date",
		"start_date",
		"end_date",
		"invoice_number",
		"amount",
		"certificate",
		"referral"

	];


	public function participant(): BelongsTo
	{
		return $this->belongsTo(Participant::class);
	}


}
