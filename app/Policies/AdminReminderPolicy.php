<?php

namespace App\Policies;

use App\Models\AdminReminder;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AdminReminderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'rrhh', 'ventas', 'servicio_al_cliente', 'gerente', 'gerente_sucursal', 'televentas']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AdminReminder $adminReminder): bool
    {
        return $user->hasAnyRole(['super_admin', 'rrhh', 'ventas', 'servicio_al_cliente', 'gerente', 'gerente_sucursal', 'televentas']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'rrhh', 'ventas', 'servicio_al_cliente', 'gerente', 'gerente_sucursal', 'televentas']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AdminReminder $adminReminder): bool
    {
        return $user->hasAnyRole(['super_admin', 'rrhh', 'ventas', 'servicio_al_cliente', 'gerente', 'gerente_sucursal', 'televentas']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AdminReminder $adminReminder): bool
    {
        return $user->hasAnyRole(['super_admin', 'rrhh', 'servicio_al_cliente', 'gerente']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AdminReminder $adminReminder): bool
    {
        return $user->hasAnyRole('super_admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AdminReminder $adminReminder): bool
    {
        return $user->hasAnyRole('super_admin');
    }
}
