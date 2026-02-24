<?php

namespace NinjaPortal\Portal\Contracts\Services;

use NinjaPortal\Portal\Models\Admin;

interface AdminServiceInterface extends ServiceInterface
{
    public function findByEmail(string $email): ?Admin;
}

