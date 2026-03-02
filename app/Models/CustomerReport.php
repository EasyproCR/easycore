<?php

namespace App\Models;

use App\Models\Traits\BelongsToCountry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

class CustomerReport extends Model
{
    use BelongsToCountry;

    use HasFactory, HasFilamentComments;

    protected $fillable = [
        'user_id',
        'personal_customer_id',
        'customer_name',
        'national_id',
        'email',
        'phone_number',
        'property_name',
        'organization_id',
        'budget_usd',
        'budget_crc',
        'financing',
        'expected_commission_usd',
        'expected_commission_crc',
        'report_status',
        'rejection_reason',
        'user_comments',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function personal_customer()
    {
        return $this->belongsTo(PersonalCustomer::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
