<?php

namespace NinjaPortal\Portal\Repositories;

use NinjaPortal\Portal\Common\Repositories\BaseRepository;
use NinjaPortal\Portal\Contracts\Repositories\AdminRepositoryInterface;
use NinjaPortal\Portal\Models\Admin;

/**
 * @extends BaseRepository<Admin>
 */
class AdminRepository extends BaseRepository implements AdminRepositoryInterface
{
    public function findByEmail(string $email): ?Admin
    {
        $model = $this->getBuilder()->where('email', $email)->first();

        return $model instanceof Admin ? $model : null;
    }
}

