<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Auto-visualisation autorisée
        if ($user->id === $model->id) {
            return true;
        }

        // Admin peut tout voir
        if ($user->isAdmin()) {
            return true;
        }

        // Manager ne peut voir que les users normaux
        return $user->isManager() && $model->isUser();    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Auto-modification autorisée
        if ($user->id === $model->id) {
            return true;
        }

        // Admin peut tout modifier
        if ($user->isAdmin()) {
            return true;
        }

        // Manager ne peut modifier que les users normaux
        return $user->isManager() && $model->isUser();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Admin peut tout supprimer
        if ($user->isAdmin()) {
            return true;
        }

        // Manager ne peut supprimer que les users normaux
        return $user->isManager() && $model->isUser();    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }
}
