<?php

namespace NinjaPortal\Portal\Policies;

use Illuminate\Foundation\Auth\User;

class AdminPolicy extends BasePolicy
{
    protected string $model = 'admin';
}
