<?php

namespace NinjaPortal\Portal\Services;

use Lordjoo\LaraApigee\Api\ApigeeX\Entities\DeveloperApp as ApigeeXDeveloperApp;
use Lordjoo\LaraApigee\Api\ApigeeX\Services\DeveloperAppService as ApigeeXDeveloperAppService;
use Lordjoo\LaraApigee\Api\Edge\Services\DeveloperAppService as EdgeDeveloperAppService;
use Lordjoo\LaraApigee\Api\Edge\Entities\DeveloperApp as EdgeDeveloperApp;
use Lordjoo\LaraApigee\Entities\EntityInterface;
use NinjaPortal\Portal\Services\Traits\InteractsWithApigeeClient;


class UserAppService extends AppService
{

    use InteractsWithApigeeClient;
    use Traits\ServiceHooksAwareTrait;
    use Traits\FireEventsTrait;

    protected string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    protected function getModel(): string
    {
        return "UserApp";
    }

    protected function api(): EdgeDeveloperAppService|ApigeeXDeveloperAppService
    {
        return $this->getClient()->developerApps($this->email);
    }

    public function credentialService($email=null,$app_name=null): UserAppCredentialService
    {
        return new UserAppCredentialService($email ?? $this->email, $app_name);
    }


}
