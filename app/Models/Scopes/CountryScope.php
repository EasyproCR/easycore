<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class CountryScope implements Scope
{
    protected static $isChecking = false;

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Prevenir loop infinito al resolver Auth::user() que lanza consultas Eloquent.
        if (self::$isChecking) {
            return;
        }

        self::$isChecking = true;

        try {
            if (Auth::check() && Auth::user()->country_id) {
                // Roles que pueden ver información transversal de todos los países
                $excludedRoles = [
                    'super_admin',
                    'soporte',
                    'gerente',
                    'rrhh',
                    'ventas',
                    'contabilidad',
                    'servicio_al_cliente'
                ];

                if (Auth::user()->hasAnyRole($excludedRoles)) {
                    return;
                }

                $builder->where($model->getTable() . '.country_id', Auth::user()->country_id);
            }
        } finally {
            self::$isChecking = false;
        }
    }
}
