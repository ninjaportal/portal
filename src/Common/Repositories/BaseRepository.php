<?php

namespace NinjaPortal\Portal\Common\Repositories;

use BadMethodCallException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use NinjaPortal\Portal\Common\Contracts\RepositoryInterface;

/**
 * @template TModel of Model
 *
 * @implements RepositoryInterface<TModel>
 */
abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var TModel
     */
    protected Model $model;

    /**
     * @var Builder<TModel>|null
     */
    protected ?Builder $query = null;

    protected array $onlyForCurrentUser = [];

    protected array $appliedScopes = [];

    protected array $alwaysLoad = [];

    public function __construct($model)
    {
        $this->setModel($model);
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function setModel($model): void
    {
        if (is_string($model)) {
            $this->model = app($model);
        } else {
            $this->model = $model;
        }
    }

    public function make(array $attributes): Model
    {
        return $this->model->fill($attributes);
    }

    public function paginate(
        int $perPage = 10,
        array $with = [],
        array $cols = ['*'],
        string $orderBy = 'id',
        string $direction = 'DESC',
        array $withCount = [],
        ?\Closure $extendQuery = null
    ): LengthAwarePaginator {
        $query = $this->getBuilder($with, $withCount, $extendQuery);

        if ($this->model->hasNamedScope('search')) {
            $query = $query->search();
        }

        if ($this->model->hasNamedScope('filter')) {
            $query = $query->filter();
        }

        return $query->orderBy($orderBy, $direction)->paginate($perPage, $cols);
    }

    public function getBuilder(
        ?array $with = [],
        ?array $withCount = null,
        ?callable $extendQuery = null
    ): Builder {
        $query = $this->query ?? $this->model->newQuery();

        foreach ($this->appliedScopes as $scope) {
            [$scopeName, $params] = explode(':', $scope) + [1 => null];
            if ($this->model->hasNamedScope($scopeName)) {
                $query = $query->{$scopeName}($params);
            }
        }

        if ($this->onlyForCurrentUser) {
            [$relation, $col] = $this->onlyForCurrentUser;
            if ($relation) {
                $query = $query->whereHas($relation, function ($q) use ($col) {
                    $q->where($col, auth()->id());
                });
            } else {
                $query = $query->where($col, auth()->id());
            }
        }

        $relations = array_filter(array_unique(array_merge(
            $this->alwaysLoad,
            $with ?? []
        )));

        if ($relations) {
            $query = $query->with($relations);
        }

        if ($withCount) {
            $query = $query->withCount($withCount);
        }

        if (is_callable($extendQuery)) {
            $extended = $extendQuery($query);
            if ($extended instanceof Builder) {
                $query = $extended;
            }
        }

        return $query;
    }

    public function list(
        array $with = [],
        array $cols = ['*'],
        string $orderBy = 'id',
        string $direction = 'DESC',
        array $withCount = []
    ): Collection {
        $query = $this->getBuilder($with, $withCount);

        if ($this->model->hasNamedScope('search')) {
            $query = $query->search();
        }

        if ($this->model->hasNamedScope('filter')) {
            $query = $query->filter();
        }

        return $query->orderBy($orderBy, $direction)->get($cols);
    }

    public function create(array $attributes): Model
    {
        if ($this->onlyForCurrentUser && is_null($this->onlyForCurrentUser[0] ?? null)) {
            [$relation, $column] = $this->onlyForCurrentUser;
            if (is_null($relation)) {
                $attributes[$column] = auth()->id();
            }
        }

        return $this->model->create($attributes);
    }

    public function update(int|string $id, array $attributes): bool
    {
        $item = $this->findOrFail($id);

        if (isset($attributes['metadata'])) {
            $attributes['metadata'] = array_merge($item->metadata ?? [], $attributes['metadata']);
        }

        return $item->update($attributes);
    }

    public function delete(int|string $id): bool
    {
        $item = $this->findOrFail($id);

        return (bool) $item->delete();
    }

    public function restore(int|string $id): bool
    {
        if (! in_array(SoftDeletes::class, class_uses_recursive($this->model), true)) {
            throw new BadMethodCallException(sprintf(
                'Model [%s] does not support restore(). Add SoftDeletes to the model before calling restore().',
                $this->model::class
            ));
        }

        $item = $this->model->withTrashed()->findOrFail($id);

        return (bool) $item->restore();
    }

    public function find(int|string $id, array $with = []): ?Model
    {
        return $this->getBuilder($with)->find($id);
    }

    public function findOrFail(int|string $id, array $with = []): Model
    {
        return $this->getBuilder($with)->findOrFail($id);
    }

    public function findBy(array $data, array $with = []): ?Model
    {
        return $this->getBuilder($with)->where($data)->first();
    }

    public function findByOrFail(array $data, array $with = []): Model
    {
        return $this->getBuilder($with)->where($data)->firstOrFail();
    }

    public function findOrCache(int|string $id, array $with = [], int $ttl = 60): Model
    {
        $key = md5(serialize([
            'table' => $this->model->getTable(),
            'id' => $id,
            'with' => $with,
        ]));

        return Cache::remember($key, $ttl, function () use ($id, $with) {
            return $this->findOrFail($id, $with);
        });
    }

    public function findByOrCache(array $data, array $with = [], int $ttl = 60): ?Model
    {
        $key = md5(serialize([
            'table' => $this->model->getTable(),
            'data' => $data,
            'with' => $with,
        ]));

        return Cache::remember($key, $ttl, function () use ($data, $with) {
            return $this->findBy($data, $with);
        });
    }

    public function onlyForCurrentUser(?string $relation = null, string $col = 'user_id'): RepositoryInterface
    {
        $this->onlyForCurrentUser = [$relation, $col];

        return $this;
    }

    public function shouldApplyScopes(array $scopes): RepositoryInterface
    {
        $this->appliedScopes = $scopes;

        return $this;
    }

    public function resolve(Model|int|string $model): Model
    {
        if ($model instanceof Model) {
            return $model;
        }

        return $this->findOrFail($model);
    }

    public function alwaysLoad(array $relations): RepositoryInterface
    {
        $this->alwaysLoad = $relations;

        return $this;
    }
}
