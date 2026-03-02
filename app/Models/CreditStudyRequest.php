<?php

namespace App\Models;

use App\Models\Traits\BelongsToCountry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

class CreditStudyRequest extends Model
{
    use BelongsToCountry;

    use HasFactory, HasFilamentComments;

    protected $fillable = [
        'user_id',
        'personal_customer_id',
        'property',
        'request_reason',
        'request_status',
        'sales_comments',
        'rejection_reason',
        'documents',
    ];

    protected $casts = [
        'documents' => 'array',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function personal_customer(){
        return $this->belongsTo(PersonalCustomer::class);
    }
}
