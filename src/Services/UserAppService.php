<?php

namespace NinjaPortal\Portal\Services;

use NinjaPortal\Portal\Services\Traits\InteractsWithApigeeClient;
use Lordjoo\LaraApigee\Contracts\Services\DeveloperAppServiceInterface;
use NinjaPortal\Portal\Utils;


class UserAppService extends AppService
{

    use Traits\ServiceHooksAwareTrait;
    use Traits\FireEventsTrait;

    protected string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public static function getModel(): string
    {
        return "UserApp";
    }

    /**
     * @throws \Exception
     */
    protected function api(): DeveloperAppServiceInterface
    {
        return Utils::getApigeeClient()->developerApps($this->email);
    }

    public function credentialService($email = null, $app_name = null): UserAppCredentialService
    {
        return new UserAppCredentialService($email ?? $this->email, $app_name);
    }


}
