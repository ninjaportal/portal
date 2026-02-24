<?php

namespace NinjaPortal\Portal\Common\Query\Search;

use Illuminate\Database\Eloquent\Builder;

abstract class SearchAbstract
{
    /**
     * Define the searchable fields and their handlers.
     *
     * Examples:
     * - ['email', 'first_name']                     // column LIKE
     * - ['full_name' => 'searchByFullName']         // custom method
     * - ['created_at' => DateSearchType::class]     // custom search type
     *
     * @var array<string, callable|string>|array<int, string>
     */
    protected array $search = [];

    public function apply(Builder $builder): Builder
    {
        $searchQuery = $this->getSearchQueryValue();

        if ($searchQuery === '') {
            return $builder;
        }

        return $builder->where(function (Builder $query) use ($searchQuery) {
            foreach ($this->search as $field => $handler) {
                $this->applySearchHandler($query, (string) $field, $handler, $searchQuery);
            }
        });
    }

    protected function applySearchHandler(Builder $query, string $field, callable|string $handler, string $searchQuery): void
    {
        if (is_string($handler) && is_callable([$this, $handler])) {
            $this->{$handler}($query, $searchQuery);

            return;
        }

        if (is_string($handler) && class_exists($handler)) {
            /** @var AttributeSearchTypeInterface $handler */
            (new $handler)->search($query, $searchQuery);

            return;
        }

        $column = is_string($handler) ? $handler : $field;
        if ($column !== '') {
            $query->orWhere($column, 'like', '%'.$searchQuery.'%');
        }
    }

    protected function getSearchQueryValue(): string
    {
        $q = (string) request()->query('q', '');
        if ($q !== '') {
            return trim($q);
        }

        // Backwards/portal compatibility: many admin lists use `search=...`
        return trim((string) request()->query('search', ''));
    }
}

