<?php

namespace NinjaPortal\Portal\Query\Filters;

use Illuminate\Database\Eloquent\Builder;
use NinjaPortal\Portal\Common\Query\Filters\FilterAbstract;

class AdminFilter extends FilterAbstract
{
    protected array $filters = [
        'role_id' => 'filterRoleId',
        'role_name' => 'filterRoleName',
    ];

    protected function filterRoleId(Builder $builder, mixed $value): void
    {
        $roleId = is_numeric($value) ? (int) $value : 0;
        if ($roleId <= 0) {
            return;
        }

        $builder->whereHas('roles', function (Builder $roleQuery) use ($roleId): void {
            $roleQuery->whereKey($roleId);
        });
    }

    protected function filterRoleName(Builder $builder, mixed $value): void
    {
        $roleName = trim((string) $value);
        if ($roleName === '') {
            return;
        }

        $builder->whereHas('roles', function (Builder $roleQuery) use ($roleName): void {
            $roleQuery->where('name', $roleName);
        });
    }
}
