<?php

namespace NinjaPortal\Portal\Query\Search;

use Illuminate\Database\Eloquent\Builder;
use NinjaPortal\Portal\Common\Query\Search\SearchAbstract;

class MenuItemSearch extends SearchAbstract
{
    protected array $search = [
        'slug',
        'translations' => 'searchTranslations',
    ];

    protected function searchTranslations(Builder $query, string $searchQuery): void
    {
        $needle = '%'.$searchQuery.'%';

        $query
            ->orWhereTranslationLike('title', $needle)
            ->orWhereTranslationLike('url', $needle)
            ->orWhereTranslationLike('route', $needle);
    }
}
