<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Laravel\Cashier\Billable;

class User extends Authenticatable implements HasAvatar
{
    use HasApiTokens, HasFactory, Notifiable;
    use Billable;
    use HasRoles;
    use HasPanelShield;

    protected static function booted()
    {
        $codeGenerator = function ($user) {
            if ($user->country_id) {
                $country = \App\Models\Country::find($user->country_id);
                if ($country) {
                    $iso = $country->iso2 ?? 'XX';
                    // Use ID or fallback to 0000 if ID not set yet (should be set on created/updated)
                    $user->code = 'EP-' . strtoupper($iso) . '-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
                }
            }
        };

        static::created(function ($user) use ($codeGenerator) {
            $codeGenerator($user);
            if ($user->isDirty('code')) {
                $user->saveQuietly();
            }
        });

        static::updated(function ($user) use ($codeGenerator) {
            // Only regenerate if country_id changed or code is missing
            if ($user->isDirty('country_id') || empty($user->code)) {
                $codeGenerator($user);
                if ($user->isDirty('code')) {
                    $user->saveQuietly();
                }
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'email',
        'state',
        'password',
        'country_id',
        'email_verified_at',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getFilamentAvatarUrl(): ?string
    {
        $avatar = $this->avatar_url;

        if (!$avatar) {
            return null;
        }

        /** @var string $disk */
        $disk = config('filament-edit-profile.disk', 'public');

        return Storage::disk($disk)->url($avatar);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function calendars()
    {
        return $this->belongsToMany(Calendar::class);
    }

    public function departaments()
    {
        return $this->belongsToMany(Departament::class);
    }

    public function leaves()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function customerreports()
    {
        return $this->hasMany(CustomerReport::class);
    }

    public function operations()
    {
        return $this->hasMany(Operation::class);
    }

    public function properties()
    {
        return $this->hasMany(PropertyAssignment::class);
    }

    public function personalcustomers()
    {
        return $this->hasMany(PersonalCustomer::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function accesRequests()
    {
        return $this->hasMany(AccesRequest::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function creditStudyRequests()
    {
        return $this->hasMany(CreditStudyRequest::class);
    }

    public function adRequests()
    {
        return $this->hasMany(AdRequest::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function thirdPartyProperties()
    {
        return $this->hasMany(ThirdPartyProperty::class);
    }

    public function collab()
    {
        return $this->hasMany(CollaborationRequest::class);
    }

    public function financialControls()
    {
        return $this->hasMany(FinancialControl::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
