<?php

namespace NinjaPortal\Portal\Services;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Macroable;
use NinjaPortal\Portal\Contracts\Services\ServiceInterface;

abstract class BaseService implements ServiceInterface
{

    use Macroable;
    use Traits\ServiceHooksAwareTrait;
    use Traits\FireEventsTrait;

    static protected string $model;

    public function all(): Collection|array
    {
        return $this->query()->get();
    }

    public function find(int $id): mixed
    {
        return $this->query()->find($id);
    }

    public function create(array $data): mixed
    {
        $this->callHook('beforeCreate', [$data]);

        $data = $this->mutateDataBeforeCreate($data);

        $item = $this->query()->create($data);

        $this->callHook('afterCreate', [$item]);

        $this->fireEvent('created', [$item]);

        return $item;
    }

    public function update(int|Model $item, array $data): array|\Illuminate\Database\Eloquent\Model
    {
        $this->callHook('beforeUpdate', [$item, $data]);

        // if not integer, then it is a model
        if (!$item instanceof Model) {
            $item = $this->query()->find($item);
        }

        if (!$item)
            throw new Exception("Record not found");

        $data = $this->mutateDataBeforeUpdate($data);

        $item->update($data);

        $this->callHook('afterUpdate', [$item, $data]);

        $this->fireEvent('updated', [$item]);

        return $item;

    }

    public function delete(int $id): void
    {
        $item = $this->query()->find($id);

        $this->callHook('beforeDelete', [$item]);

        $item->delete();

        $this->callHook('afterDelete', [$item]);

        $this->fireEvent('deleted', [$item]);
    }


    public function query(): Builder
    {
        return static::getModel()::query();
    }

    public static function getModel(): string
    {
        return static::$model ?? (string) str(class_basename(static::class))
            ->beforeLast("Service")->plural('App\\Models\\');
    }

    protected function mutateDataBeforeUpdate(array $data): array
    {
        return $data;
    }

    protected function mutateDataBeforeCreate(array $data): array
    {
        return $data;
    }


}
