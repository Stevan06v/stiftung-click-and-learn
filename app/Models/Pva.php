<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pva extends Model
{
    use HasFactory;

    protected $table = 'pvas';

    protected $fillable = [
        'salutation',
        'title',
        'firstname',
        'lastname',
        'phone_number',
        'email',
        'name',
        'street',
        'postcode',
        'city',
        'region',
        'note'
    ];

	public function participants(): HasMany
	{
		return $this->hasMany(Participant::class);
	}

	public function notes(): HasMany
	{
		return $this->hasMany(Note::class);
	}
}
