<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
	use HasFactory;

	protected $fillable = [
		"title",
		"text",
		"note_date",
		"note_files",
		"original_filenames",
		"participant_id",
		"user_id"
	];
	protected $casts = [
		'note_files' => 'array',
		'original_filenames' => 'array'
	];

	public function participant(): BelongsTo
	{
		return $this->belongsTo(Participant::class);
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
