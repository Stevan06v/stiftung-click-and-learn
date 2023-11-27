<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'companyname1',
        'companyname2',
        'salutation',
        'street',
        'postcode',
        'city',
        'phone_number',
        'fax',
        'phone_number_mobil',
        'email',
        'website',
        'cooperation_agreement',
        'note',
        'hour_record',
    ];

    public function companyAdvisors(): HasMany
	{
        return $this->hasMany(CompanyAdvisor::class);
    }

}
