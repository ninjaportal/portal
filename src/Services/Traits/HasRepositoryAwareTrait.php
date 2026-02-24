<?php

namespace NinjaPortal\Portal\Services\Traits;

use Illuminate\Database\Eloquent\Model;
use NinjaPortal\Portal\Common\Contracts\RepositoryInterface;

/**
 * @template TModel of Model
 * @template TRepository of RepositoryInterface<TModel>
 */
trait HasRepositoryAwareTrait
{
    /**
     * @var TRepository
     */
    protected RepositoryInterface $repository;

    /**
     * @return TRepository
     */
    public function repository(): RepositoryInterface
    {
        return $this->repository;
    }

    public function getModel()
    {
        return $this->repository()->getModel();
    }
}
