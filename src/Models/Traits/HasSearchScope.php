<?php

namespace NinjaPortal\Portal\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * @property-read array $searchable
 */
trait HasSearchScope
{
    /**
     * Apply a simple "LIKE" search across the model's `$searchable` columns.
     *
     * Expected usage:
     * - Define `public array $searchable = ['name', 'email', 'relation.field'];` on the model.
     * - Call `$query->search()` (will read `request('search')` / `request('q')`) or `$query->search('term')`.
     */
    public function scopeSearch(Builder $query, ?string $search = null): Builder
    {
        $search ??= request()->input('search') ?? request()->input('q');

        if ($search === null || trim($search) === '') {
            return $query;
        }

        $columns = (array) ($this->searchable ?? []);
        if ($columns === []) {
            return $query;
        }

        $needle = trim($search);

        return $query->where(function (Builder $query) use ($needle, $columns) {
            foreach ($columns as $column) {
                if (! is_string($column) || $column === '') {
                    continue;
                }

                if (str_contains($column, '.')) {
                    [$relation, $field] = explode('.', $column, 2);

                    if ($relation === '' || $field === '') {
                        continue;
                    }

                    $query->orWhereHas($relation, function (Builder $query) use ($needle, $field) {
                        $query->where($field, 'like', '%'.$needle.'%');
                    });

                    continue;
                }

                $query->orWhere($column, 'like', '%'.$needle.'%');
            }
        });
    }
}
