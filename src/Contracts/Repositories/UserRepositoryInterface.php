<?php

namespace NinjaPortal\Portal\Contracts\Repositories;

use NinjaPortal\Portal\Common\Contracts\RepositoryInterface;
use NinjaPortal\Portal\Models\User;

/**
 * @extends RepositoryInterface<User>
 */
interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?User;
}
