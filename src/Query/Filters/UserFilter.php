<?php

namespace NinjaPortal\Portal\Query\Filters;

use Illuminate\Database\Eloquent\Builder;
use NinjaPortal\Portal\Common\Query\Filters\FilterAbstract;
use NinjaPortal\Portal\Models\User;

class UserFilter extends FilterAbstract
{
    protected array $filters = [
        'status' => 'filterStatus',
        'sync_with_apigee' => 'sync_with_apigee',
    ];

    protected function filterStatus(Builder $builder, mixed $value): void
    {
        $status = strtolower(trim((string) $value));
        if ($status === '' || ! in_array($status, array_values(User::$USER_STATUS), true)) {
            return;
        }

        $builder->where('status', $status);
    }
}

