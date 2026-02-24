<?php

namespace NinjaPortal\Portal\Contracts\Services;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Base contract for portal services backed by Eloquent models.
 */
interface ServiceInterface
{
    /**
     * Retrieve all records for the service's model.
     */
    public function all(): Collection|array;

    /**
     * Paginate records for the service's model.
     *
     * @param  array<int, string>  $with
     * @param  array<int, string>  $cols
     * @param  array<int, string>  $withCount
     */
    public function paginate(
        int $perPage = 10,
        array $with = [],
        array $cols = ['*'],
        string $orderBy = 'id',
        string $direction = 'DESC',
        array $withCount = [],
        ?Closure $extendQuery = null,
    ): LengthAwarePaginator;

    /**
     * Find a single record by its identifier.
     */
    public function find(int|string $id): mixed;

    /**
     * Persist a new record using the supplied attributes.
     */
    public function create(array $data): mixed;

    /**
     * Update an existing record instance or identifier with new attributes.
     */
    public function update(int|string|Model $item, array $data): mixed;

    /**
     * Remove the record with the given identifier.
     */
    public function delete(int|string $id): void;

    /**
     * Restore a soft-deleted record by identifier.
     *
     * @throws \BadMethodCallException when the underlying model does not use SoftDeletes
     */
    public function restore(int|string $id): bool;

    /**
     * Expose the underlying query builder for further composition.
     */
    public function query(): Builder;
}
