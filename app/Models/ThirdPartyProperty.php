<?php

namespace App\Models;

use App\Models\Traits\BelongsToCountry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;

class ThirdPartyProperty extends Model
{
    use BelongsToCountry;

    use HasFactory, SoftDeletes, HasFilamentComments;

    protected $fillable = [
        // Info
        'name',
        'status',
        'finca_number',
        'country_id',
        'state_id',
        'city_id',
        'address',
        'received_at',
        'service_type',
        'currency_code',
        'monthly_amount',
        'monthly_amount_usd',
        'sale_amount',
        'sale_amount_usd',
        'details',

        // Responsables
        'user_id',
        'personal_customer_id',
        'supervisor_id',

        // Adjuntos
        'contract_path',
        'registry_study_path',
    ];

    protected $casts = [
        'received_at'    => 'date',
        'monthly_amount' => 'decimal:2',
        'sale_amount'    => 'decimal:2',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function owner()
    {
        return $this->belongsTo(PersonalCustomer::class, 'personal_customer_id');
    }

    protected static function booted()
    {
        static::saving(function (ThirdPartyProperty $model) {
            // Adjuntos obligatorios
            if (blank($model->contract_path) || blank($model->registry_study_path)) {
                throw ValidationException::withMessages([
                    'contract_path'   => 'El contrato es obligatorio',
                    'registry_study_path' => 'El estudio de registro es obligatorio',
                ]);
            }

            // Supervisor ≠ User
            if (
                $model->user_id && $model->supervisor_id
                && $model->user_id === $model->supervisor_id
            ) {
                throw ValidationException::withMessages([
                    'supervisor_id' => 'El supervisor no puede ser el mismo que el usuario responsable',
                ]);
            }
        });
    }
}
