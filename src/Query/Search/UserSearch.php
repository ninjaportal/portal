<?php

namespace NinjaPortal\Portal\Query\Search;

use Illuminate\Database\Eloquent\Builder;
use NinjaPortal\Portal\Common\Query\Search\SearchAbstract;

class UserSearch extends SearchAbstract
{
    protected array $search = [
        'email',
        'first_name',
        'last_name',
        'full_name' => 'searchByFullName',
    ];

    protected function searchByFullName(Builder $query, string $searchQuery): void
    {
        $needle = '%'.$searchQuery.'%';
        $query->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$needle]);
    }
}

