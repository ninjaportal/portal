<?php

namespace NinjaPortal\Portal\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use NinjaPortal\Portal\Query\Filters\AdminFilter;
use NinjaPortal\Portal\Query\Search\AdminSearch;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasRoles;

    protected string $guard_name = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    public function guardName(): string
    {
        return \NinjaPortal\Portal\Utils::getAdminRbacGuard();
    }

    public function scopeSearch(Builder $builder): Builder
    {
        return (new AdminSearch)->apply($builder);
    }

    public function scopeFilter(Builder $builder): Builder
    {
        return (new AdminFilter)->apply($builder);
    }

    protected function getDefaultGuardName(): string
    {
        return $this->guardName();
    }
}
