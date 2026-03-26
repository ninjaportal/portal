<?php

namespace NinjaPortal\Portal\Query\Search;

use Illuminate\Database\Eloquent\Builder;
use NinjaPortal\Portal\Common\Query\Search\SearchAbstract;

class SettingSearch extends SearchAbstract
{
    protected array $search = [
        'key',
        'label',
        'value',
        'group' => 'searchGroup',
    ];

    protected function searchGroup(Builder $query, string $searchQuery): void
    {
        $needle = '%'.$searchQuery.'%';

        $query->orWhereHas('group', function (Builder $groupQuery) use ($needle): void {
            $groupQuery->where('name', 'like', $needle);
        });
    }
}
