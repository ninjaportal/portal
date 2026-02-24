<?php

namespace NinjaPortal\Portal\Contracts\Repositories;

use NinjaPortal\Portal\Common\Contracts\RepositoryInterface;
use NinjaPortal\Portal\Models\Admin;

/**
 * @extends RepositoryInterface<Admin>
 */
interface AdminRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?Admin;
}

