<?php

namespace NinjaPortal\Portal\Query\Search;

use Illuminate\Database\Eloquent\Builder;
use NinjaPortal\Portal\Common\Query\Search\SearchAbstract;

class AdminSearch extends SearchAbstract
{
    protected array $search = [
        'name',
        'email',
        'roles' => 'searchRoles',
    ];

    protected function searchRoles(Builder $query, string $searchQuery): void
    {
        $needle = '%'.$searchQuery.'%';

        $query->orWhereHas('roles', function (Builder $roleQuery) use ($needle): void {
            $roleQuery->where('name', 'like', $needle);
        });
    }
}
