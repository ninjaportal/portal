<?php

namespace NinjaPortal\Portal\Repositories;

use NinjaPortal\Portal\Common\Repositories\BaseRepository;
use NinjaPortal\Portal\Contracts\Repositories\SettingGroupRepositoryInterface;
use NinjaPortal\Portal\Models\SettingGroup;

/**
 * @extends BaseRepository<SettingGroup>
 */
class SettingGroupRepository extends BaseRepository implements SettingGroupRepositoryInterface {}
