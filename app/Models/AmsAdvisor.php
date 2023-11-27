<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmsAdvisor extends Model
{
    use HasFactory;

    protected $table = 'ams_advisors';

    protected $fillable = [
        'salutation',
        'title',
        'firstname',
        'lastname',
        'email',
        'ams_rgs_id',
        'function',
        'department_head',
        'phone_number',
        'note',
    ];

	public function participants(): HasMany
	{
		return $this->hasMany(Participant::class);
	}

	public function ams_rgs(): BelongsTo
	{
		return $this->belongsTo(AmsRgs::class);
	}
}
