<?php

namespace NinjaPortal\Portal\Services;

use NinjaPortal\Portal\Contracts\Repositories\AdminRepositoryInterface;
use NinjaPortal\Portal\Contracts\Services\AdminServiceInterface;
use NinjaPortal\Portal\Events\Admin\AdminRolesSyncedEvent;
use NinjaPortal\Portal\Models\Admin;
use NinjaPortal\Portal\Services\Traits\CrudOperationsTrait;
use NinjaPortal\Portal\Utils;
use Spatie\Permission\Models\Role;

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

    public function syncRoles(Admin|int|string $admin, array $roleIds): Admin
    {
        $admin = $this->repository()->resolve($admin);

        $this->callHook('beforeSyncRoles', [$admin, $roleIds]);

        if (method_exists($admin, 'syncRoles')) {
            $guard = method_exists($admin, 'getDefaultGuardName')
                ? (string) $admin->getDefaultGuardName()
                : 'admin';

            $roles = Role::query()
                ->where('guard_name', $guard)
                ->whereIn('id', $roleIds)
                ->pluck('name')
                ->all();

            $admin->syncRoles($roles);
        }

        $admin->load('roles');

        $this->callHook('afterSyncRoles', [$admin, $roleIds]);

        AdminRolesSyncedEvent::dispatch($admin, $roleIds);

        return $admin;
    }
}
