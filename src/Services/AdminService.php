<?php

namespace NinjaPortal\Portal\Services;

use NinjaPortal\Portal\Contracts\Repositories\AdminRepositoryInterface;
use NinjaPortal\Portal\Contracts\Services\AdminServiceInterface;
use NinjaPortal\Portal\Services\Traits\CrudOperationsTrait;
use NinjaPortal\Portal\Utils;
use NinjaPortal\Portal\Models\Admin;

/**
 * @mixin Traits\HasRepositoryAwareTrait<Admin, AdminRepositoryInterface>
 */
class AdminService extends BaseService implements AdminServiceInterface
{
    use CrudOperationsTrait;

    public function __construct(AdminRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function getModel(): string
    {
        return Utils::getAdminModel() ?: Admin::class;
    }

    public function findByEmail(string $email): ?Admin
    {
        return $this->repository()->findByEmail($email);
    }
}
