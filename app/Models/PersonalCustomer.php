<?php

namespace App\Models;

use App\Models\Traits\BelongsToCountry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

class PersonalCustomer extends Model
{
    use BelongsToCountry;

    use HasFactory;
    use HasFilamentComments;

    protected $fillable = [
        'user_id',
        'prospect_status',
        'full_name',
        'national_id',
        'email',
        'phone_number',
        'license_plate',
        'customer_need',
        'address',
        'contact_preferences',
        'date_of_birth',
        'customer_type',
        'next_action',
        'next_action_date',
        'budget_usd',
        'budget_crc',
        'financing',
        'expected_commission_usd',
        'expected_commission_crc',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function offers(){
        return $this->hasMany(Offer::class);
    }

    public function acces_requests(){
        return $this->hasMany(AccesRequest::class);
    }

    public function customer_reports(){
        return $this->hasMany(CustomerReport::class);
    }

    public function credit_study_requests(){
        return $this->hasMany(CreditStudyRequest::class);
    }
}
