<?php

namespace NinjaPortal\Portal\Query\Filters;

use Illuminate\Database\Eloquent\Builder;
use NinjaPortal\Portal\Common\Query\Filters\FilterAbstract;

class SettingFilter extends FilterAbstract
{
    protected array $filters = [
        'type' => 'type',
        'setting_group_id' => 'setting_group_id',
        'group_name' => 'filterGroupName',
    ];

    protected function filterGroupName(Builder $builder, mixed $value): void
    {
        $groupName = trim((string) $value);
        if ($groupName === '') {
            return;
        }

        $builder->whereHas('group', function (Builder $groupQuery) use ($groupName): void {
            $groupQuery->where('name', $groupName);
        });
    }
}
