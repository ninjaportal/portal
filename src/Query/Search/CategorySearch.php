<?php

namespace NinjaPortal\Portal\Query\Search;

use Illuminate\Database\Eloquent\Builder;
use NinjaPortal\Portal\Common\Query\Search\SearchAbstract;

class CategorySearch extends SearchAbstract
{
    protected array $search = [
        'slug',
        'translations' => 'searchTranslations',
    ];

    protected function searchTranslations(Builder $query, string $searchQuery): void
    {
        $needle = '%'.$searchQuery.'%';

        $query
            ->orWhereTranslationLike('name', $needle)
            ->orWhereTranslationLike('short_description', $needle)
            ->orWhereTranslationLike('description', $needle);
    }
}

