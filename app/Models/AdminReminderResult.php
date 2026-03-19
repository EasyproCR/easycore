<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminReminderResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_reminder_id',
        'revision_date',
        'result',
        'detail',
    ];

    protected $casts = [
        'revision_date' => 'date',
    ];

    public function adminReminder()
    {
        return $this->belongsTo(AdminReminder::class);
    }
}
