<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmsRgs extends Model
{
    use HasFactory;

    protected $table = 'ams_rgs';

    protected $fillable = [
        'name',
        'street',
        'postcode',
        'city',
        'email',
        'phone_number',
        'note',
    ];

    public function amsAdvisors(): HasMany
    {
        return $this->hasMany(AmsAdvisor::class);
    }

}
