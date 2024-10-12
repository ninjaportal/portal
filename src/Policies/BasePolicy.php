<?php

namespace NinjaPortal\Portal\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

abstract class BasePolicy
{
    protected string $model;

    use HandlesAuthorization;

    public function before(User $user)
    {
        if (
            (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin())
            || (method_exists($user, 'hasRole') && $user->hasRole('super_admin'))
        ) {
            return true;
        }

        if (!method_exists($user, 'can')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_'. $this->model);
    }

    public function view(User $user, Model $apiProduct): bool
    {
        return $user->can('view_'. $this->model);
    }

    public function create(User $user): bool
    {
        return $user->can('create_'. $this->model);
    }

    public function update(User $user, Model $apiProduct): bool
    {
        return $user->can('update_'. $this->model);
    }

    public function delete(User $user, Model $apiProduct): bool
    {
        return $user->can('delete_'. $this->model);
    }

    public function restore(User $user, Model $apiProduct): bool
    {
        return $user->can('restore_'. $this->model);
    }

    public function forceDelete(User $user, Model $apiProduct): bool
    {
        return $user->can('force_delete_'. $this->model);
    }

}
