<?php

namespace App\Models\Traits;

use App\Models\Scopes\CountryScope;
use Illuminate\Support\Facades\Auth;

trait BelongsToCountry
{
    /**
     * The "booted" method of the trait.
     */
    protected static function bootBelongsToCountry(): void
    {
        // Apply the global scope to filter queries by country_id
        static::addGlobalScope(new CountryScope);

        // Auto-assign the country_id when creating a new record
        static::creating(function ($model) {
            if (Auth::check() && empty($model->country_id) && Auth::user()->country_id) {
                $model->country_id = Auth::user()->country_id;
            }
        });
    }

    /**
     * Define the relationship to the Country model if needed by default in any model using the trait.
     */
    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class);
    }
}
