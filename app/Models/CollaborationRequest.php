<?php

namespace App\Models;

use App\Models\Traits\BelongsToCountry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

class CollaborationRequest extends Model
{
    use BelongsToCountry;

    use HasFactory, HasFilamentComments;

    protected $fillable = [
        'user_id',
        'personal_customer_id',
        'currency_code',
        'client_budget',
        'client_budget_usd',
        'areas_of_interest',
        'search_details',
    ];

    protected $casts = [
        'client_budget' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function personal_customer()
    {
        return $this->belongsTo(PersonalCustomer::class);
    }
}
