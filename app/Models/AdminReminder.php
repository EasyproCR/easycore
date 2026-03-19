<?php

namespace App\Models;

use App\Enums\ReminderFrequency;
use App\Enums\ReminderType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminReminder extends Model
{
    use \App\Traits\HasFrequency;

    protected $fillable = [
        'user_id',
        'frequency',
        'type',
        'content',
        'is_active',
        'starts_at',
        'ends_at',
        'last_sent_at',
        'next_run_at',
        'failure_count',
        'last_error_message',
        'last_error_trace',
        'last_failed_at',
    ];

    protected $casts = [
        'frequency' => ReminderFrequency::class,
        'type' => ReminderType::class,
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'last_sent_at' => 'datetime',
        'next_run_at' => 'datetime',
        'last_failed_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function (AdminReminder $reminder) {
            if (!$reminder->next_run_at && $reminder->starts_at) {
                $reminder->next_run_at = $reminder->calculateNextRunAt();
            }
        });
    }

    public function calculateNextRunAt(): ?\Carbon\CarbonImmutable
    {
        return $this->calculateNextRunDate(
            $this->starts_at,
            $this->last_sent_at,
            $this->frequency
        );
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function runs(): HasMany
    {
        return $this->hasMany(AdminReminderRun::class);
    }

    public function activityResults(): HasMany
    {
        return $this->hasMany(AdminReminderResult::class);
    }
}
