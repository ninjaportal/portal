<?php

namespace NinjaPortal\Portal\Services;

use NinjaPortal\Portal\Contracts\Repositories\CategoryRepositoryInterface;
use NinjaPortal\Portal\Contracts\Services\CategoryServiceInterface;
use NinjaPortal\Portal\Services\Traits\CrudOperationsTrait;
use NinjaPortal\Portal\Utils;

/**
 * @mixin Traits\HasRepositoryAwareTrait<\NinjaPortal\Portal\Models\Category, CategoryRepositoryInterface>
 */
class CategoryService extends BaseService implements CategoryServiceInterface
{
    use CrudOperationsTrait;

    public function __construct(CategoryRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function getModel(): string
    {
        return Utils::getCategoryModel();
    }
}
