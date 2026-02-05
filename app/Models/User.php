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

class User extends Authenticatable implements HasAvatar
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;
    use HasPanelShield;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'state',
        'password',
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
        if (! $this->avatar_url) {
            return null;
        }

        $disk = config('filament-edit-profile.disk', config('filesystems.default'));

        return Storage::disk($disk)->url($this->avatar_url);
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
}
