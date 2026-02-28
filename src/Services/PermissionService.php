<?php

namespace NinjaPortal\Portal\Services;

use NinjaPortal\Portal\Contracts\Repositories\PermissionRepositoryInterface;
use NinjaPortal\Portal\Contracts\Services\PermissionServiceInterface;
use NinjaPortal\Portal\Services\Traits\CrudOperationsTrait;
use NinjaPortal\Portal\Utils;

/**
 * @mixin Traits\HasRepositoryAwareTrait<\Spatie\Permission\Models\Permission, PermissionRepositoryInterface>
 */
class PermissionService extends BaseService implements PermissionServiceInterface
{
    use CrudOperationsTrait;

    public function __construct(PermissionRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function getModel(): string
    {
        return Utils::getPermissionModel();
    }
}
