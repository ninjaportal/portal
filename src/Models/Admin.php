<?php

namespace NinjaPortal\Portal\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable implements JWTSubject
{
    use HasRoles;

    protected string $guard_name = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    public function guardName(): string
    {
        return \NinjaPortal\Portal\Utils::getAdminRbacGuard();
    }

    protected function getDefaultGuardName(): string
    {
        return $this->guardName();
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'ctx' => 'admin',
        ];
    }
}
