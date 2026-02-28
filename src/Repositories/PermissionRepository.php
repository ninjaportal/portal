<?php

namespace NinjaPortal\Portal\Repositories;

use NinjaPortal\Portal\Common\Repositories\BaseRepository;
use NinjaPortal\Portal\Contracts\Repositories\PermissionRepositoryInterface;
use Spatie\Permission\Models\Permission;

/**
 * @extends BaseRepository<Permission>
 */
class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface {}
