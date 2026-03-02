<?php

namespace App\Models;

use App\Models\Traits\BelongsToCountry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

class LeaveRequest extends Model
{
    use BelongsToCountry;

    use HasFactory, HasFilamentComments;

    protected $fillable = [
        'user_id',
        'request_type',
        'observations',
        'request_status',
        'vacation_balance',
        'permission_date',
        'permission_options',
        'start_time',
        'end_time',
        'start_date',
        'end_date',
        'total_days',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
