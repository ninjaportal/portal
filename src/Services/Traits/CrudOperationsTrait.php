<?php

namespace NinjaPortal\Portal\Services\Traits;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 * @template TRepository of \NinjaPortal\Portal\Common\Contracts\RepositoryInterface<TModel>
 *
 * @mixin HasRepositoryAwareTrait<TModel, TRepository>
 */
trait CrudOperationsTrait
{
    use FireEventsTrait,
        HasRepositoryAwareTrait,
        ServiceHooksAwareTrait;

    public function all(): Collection|array
    {
        return $this->repository()->list();
    }

    public function paginate(
        int $perPage = 10,
        array $with = [],
        array $cols = ['*'],
        string $orderBy = 'id',
        string $direction = 'DESC',
        array $withCount = [],
        ?Closure $extendQuery = null,
    ): LengthAwarePaginator {
        return $this->repository()->paginate(
            perPage: $perPage,
            with: $with,
            cols: $cols,
            orderBy: $orderBy,
            direction: $direction,
            withCount: $withCount,
            extendQuery: $extendQuery,
        );
    }

    public function find(int|string $id): mixed
    {
        return $this->repository()->find($id);
    }

    public function create(array $data): mixed
    {
        $this->callHook('beforeCreate', [$data]);

        $data = $this->mutateDataBeforeCreate($data);

        $item = $this->repository()->create($data);

        $this->callHook('afterCreate', [$item]);

        $this->fireEvent('created', [$item]);

        return $item;
    }

    public function update(int|string|Model $item, array $data): Model
    {
        $this->callHook('beforeUpdate', [$item, $data]);

        $model = $this->repository()->resolve($item);

        $data = $this->mutateDataBeforeUpdate($data);

        $this->repository()->update($model->getKey(), $data);
        $model->refresh();

        $this->callHook('afterUpdate', [$model, $data]);

        $this->fireEvent('updated', [$model]);

        return $model;
    }

    public function delete(int|string $id): void
    {
        $model = $this->repository()->resolve($id);

        $this->callHook('beforeDelete', [$model]);

        $this->repository()->delete($model->getKey());

        $this->callHook('afterDelete', [$model]);

        $this->fireEvent('deleted', [$model]);
    }

    public function restore(int|string $id): bool
    {
        $model = $this->repository()->resolve($id);

        $this->callHook('beforeRestore', [$model]);

        $restored = $this->repository()->restore($model->getKey());

        $this->callHook('afterRestore', [$model, $restored]);

        if ($restored) {
            $this->fireEvent('restored', [$model]);
        }

        return $restored;
    }

    public function query(): Builder
    {
        return $this->repository()->getBuilder();
    }
}
