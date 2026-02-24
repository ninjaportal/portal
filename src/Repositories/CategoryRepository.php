<?php

namespace NinjaPortal\Portal\Repositories;

use NinjaPortal\Portal\Common\Repositories\BaseRepository;
use NinjaPortal\Portal\Contracts\Repositories\CategoryRepositoryInterface;

/**
 * @extends BaseRepository<\NinjaPortal\Portal\Models\Category>
 */
class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface {}
