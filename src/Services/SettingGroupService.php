<?php

namespace NinjaPortal\Portal\Services;

use NinjaPortal\Portal\Contracts\Repositories\SettingGroupRepositoryInterface;
use NinjaPortal\Portal\Contracts\Services\SettingGroupServiceInterface;
use NinjaPortal\Portal\Services\Traits\CrudOperationsTrait;
use NinjaPortal\Portal\Utils;

/**
 * @mixin Traits\HasRepositoryAwareTrait<\NinjaPortal\Portal\Models\SettingGroup, SettingGroupRepositoryInterface>
 */
class SettingGroupService extends BaseService implements SettingGroupServiceInterface
{
    use CrudOperationsTrait;

    public function __construct(SettingGroupRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function getModel(): string
    {
        return Utils::getSettingGroupModel();
    }
}
