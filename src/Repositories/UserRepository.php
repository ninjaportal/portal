<?php

namespace NinjaPortal\Portal\Repositories;

use NinjaPortal\Portal\Common\Repositories\BaseRepository;
use NinjaPortal\Portal\Contracts\Repositories\UserRepositoryInterface;
use NinjaPortal\Portal\Models\User;

/**
 * @extends BaseRepository<User>
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): ?User
    {
        $model = $this->getBuilder()->where('email', $email)->first();

        return $model instanceof User ? $model : null;
    }
}
