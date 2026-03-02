<?php

namespace App\Models;

use App\Models\Traits\BelongsToCountry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

class AccesRequest extends Model
{
    use BelongsToCountry;

    use HasFactory;
    use HasFilamentComments;

    protected $fillable = [
        'user_id',
        'type_of_request',
        'property',
        'organization_id',
        'pickup_datetime',
        'visit_datetime',
        'personal_customer_id',
        'request_status',
        'rejection_reason',
        'user_comments',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function organization(){
        return $this->belongsTo(Organization::class);
    }

    public function personal_customer(){
        return $this->belongsTo(PersonalCustomer::class);
    }
}
