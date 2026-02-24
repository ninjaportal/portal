<?php

namespace NinjaPortal\Portal\Repositories;

use NinjaPortal\Portal\Common\Repositories\BaseRepository;
use NinjaPortal\Portal\Contracts\Repositories\MenuItemRepositoryInterface;

/**
 * @extends BaseRepository<\NinjaPortal\Portal\Models\MenuItem>
 */
class MenuItemRepository extends BaseRepository implements MenuItemRepositoryInterface {}
