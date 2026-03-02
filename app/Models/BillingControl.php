<?php

namespace App\Models;

use App\Models\Traits\BelongsToCountry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

class BillingControl extends Model
{
    use BelongsToCountry;

    use HasFactory, HasFilamentComments;

    protected $fillable = [
        'offer_id',
        'invoice_status',
        'payment_percentage',
        'billing_date',
        'funds_received_date',
        'invoice_files',
    ];

    protected $casts = [
        'invoice_files' => 'array',
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
