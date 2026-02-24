<?php

namespace NinjaPortal\Portal\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

abstract class BasePolicy
{
    protected string $model;

    use HandlesAuthorization;

    public function before(Authenticatable $user, string $ability): ?bool
    {
        if (
            (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin())
            || (method_exists($user, 'hasRole') && $user->hasRole('super_admin'))
        ) {
            return true;
        }

        if (! method_exists($user, 'can')) {
            return true;
        }

        return null;
    }

    public function viewAny(Authenticatable $user): bool
    {
        return $this->allows($user, 'view_any');
    }

    public function view(Authenticatable $user, Model $model): bool
    {
        return $this->allows($user, 'view');
    }

    public function create(Authenticatable $user): bool
    {
        return $this->allows($user, 'create');
    }

    public function update(Authenticatable $user, Model $model): bool
    {
        return $this->allows($user, 'update');
    }

    public function delete(Authenticatable $user, Model $model): bool
    {
        return $this->allows($user, 'delete');
    }

    public function restore(Authenticatable $user, Model $model): bool
    {
        return $this->allows($user, 'restore');
    }

    public function forceDelete(Authenticatable $user, Model $model): bool
    {
        return $this->allows($user, 'force_delete');
    }

    protected function allows(Authenticatable $user, string $ability): bool
    {
        $ability = strtolower(trim($ability));
        $permissions = array_values(array_unique(array_filter([
            "portal.{$this->model}.{$ability}",
            $ability.'_'.$this->model, // legacy fallback (pre-standardized naming)
        ])));

        foreach ($permissions as $permission) {
            if ($user->can($permission)) {
                return true;
            }
        }

        return false;
    }
}
