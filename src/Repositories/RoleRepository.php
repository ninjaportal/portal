<?php

namespace NinjaPortal\Portal\Repositories;

use NinjaPortal\Portal\Common\Repositories\BaseRepository;
use NinjaPortal\Portal\Contracts\Repositories\RoleRepositoryInterface;
use Spatie\Permission\Models\Role;

/**
 * @extends BaseRepository<Role>
 */
class RoleRepository extends BaseRepository implements RoleRepositoryInterface {}
