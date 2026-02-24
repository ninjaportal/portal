<?php

namespace NinjaPortal\Portal\Common\Contracts;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @template TModel of Model
 */
interface RepositoryInterface
{
    /**
     * @param  class-string<TModel>|TModel  $model
     */
    public function __construct($model);

    /**
     * @param  class-string<TModel>|TModel  $model
     */
    public function setModel($model): void;

    /**
     * @return TModel
     */
    public function getModel(): Model;

    /**
     * @param  array<string, mixed>  $attributes
     * @return TModel
     */
    public function make(array $attributes): Model;

    public function paginate(
        int $perPage = 10,
        array $with = [],
        array $cols = ['*'],
        string $orderBy = 'id',
        string $direction = 'DESC',
        array $withCount = [],
        ?Closure $extendQuery = null
    ): LengthAwarePaginator;

    public function list(
        array $with = [],
        array $cols = ['*'],
        string $orderBy = 'id',
        string $direction = 'DESC',
        array $withCount = []
    ): Collection;

    /**
     * @param  array<string, mixed>  $attributes
     * @return TModel
     */
    public function create(array $attributes): Model;

    public function update(int|string $id, array $attributes): bool;

    public function delete(int|string $id): bool;

    /**
     * Restore a soft-deleted model.
     *
     * @throws \BadMethodCallException when the model does not use SoftDeletes
     */
    public function restore(int|string $id): bool;

    /**
     * @param  array<int, string>  $with
     * @return TModel|null
     */
    public function find(int|string $id, array $with = []): ?Model;

    /**
     * @param  array<int, string>  $with
     * @return TModel
     */
    public function findOrFail(int|string $id, array $with = []): Model;

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $with
     * @return TModel|null
     */
    public function findBy(array $data, array $with = []): ?Model;

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $with
     * @return TModel
     */
    public function findByOrFail(array $data, array $with = []): Model;

    /**
     * Retrieve or cache a model by ID.
     *
     * @param  array<int, string>  $with
     * @return TModel
     */
    public function findOrCache(int|string $id, array $with = [], int $ttl = 60): Model;

    /**
     * Find a model by arbitrary criteria with optional caching.
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $with
     * @return TModel|null
     */
    public function findByOrCache(array $data, array $with = [], int $ttl = 60): ?Model;

    public function onlyForCurrentUser(?string $relation = null, string $col = 'user_id'): self;

    public function shouldApplyScopes(array $scopes): self;

    /**
     * @param  TModel|int|string  $model
     * @return TModel
     */
    public function resolve(Model|int|string $model): Model;

    public function getBuilder(?array $with = [], ?array $withCount = null, ?callable $extendQuery = null): Builder;

    public function alwaysLoad(array $relations): self;
}
