<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyAdvisor extends Model
{
	use HasFactory;

	protected $table = 'company_advisors';

	protected $fillable = [
		'salutation',
		'title',
		'firstname',
		'lastname',
		'email',
		'function',
		'department_head',
		'phone_number',
		'note'
	];

	public function company(): BelongsTo
	{
		return $this->belongsTo(Company::class);
	}

	public function participants(): HasMany
	{
		return $this->hasMany(Participant::class);
	}
}
