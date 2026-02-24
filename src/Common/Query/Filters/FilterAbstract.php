<?php

namespace NinjaPortal\Portal\Common\Query\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

abstract class FilterAbstract
{
    /**
     * Define the filters and their handlers.
     *
     * Format:
     * - 'filter_name' => 'handler_method'
     * - 'filter_name' => FilterAttrClass::class
     * - 'filter_name' => 'column_name'
     *
     * @var array<string, string>
     */
    protected array $filters = [];

    public function getFilterHandlers(): array
    {
        return $this->filters;
    }

    public function apply(Builder $builder): Builder
    {
        $this->prepareRequest();

        foreach ($this->getFilters() as $filter => $value) {
            if ($value === null) {
                continue;
            }

            try {
                $this->applyFilter($builder, $filter, $value);
            } catch (\Throwable $e) {
                Log::error('Failed to apply filter '.$filter.' with error '.$e->getMessage());
            }
        }

        return $builder;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getFilters(): array
    {
        return request()->only(array_keys($this->getFilterHandlers()));
    }

    protected function applyFilter(Builder $builder, string $filter, mixed $value): void
    {
        $handler = $this->getFilterHandlers()[$filter] ?? null;
        if (! $handler) {
            return;
        }

        if (is_callable([$this, $handler])) {
            $this->{$handler}($builder, $value);

            return;
        }

        if (class_exists($handler)) {
            (new $handler)->filter($builder, $value);

            return;
        }

        $builder->where($handler, $value);
    }

    protected function prepareRequest(): void
    {
        $filters = json_decode((string) request()->query('filters', '[]'), true) ?? [];

        foreach ($filters as $filter) {
            if (isset($filter['field'], $filter['value'])) {
                request()->merge([
                    $filter['field'] => $filter['value'],
                ]);
            }
        }
    }
}

