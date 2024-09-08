<?php

namespace NinjaPortal\Portal\Services\Traits;

use Lordjoo\LaraApigee\Api\ApigeeX\ApigeeX;
use Lordjoo\LaraApigee\Api\ApigeeX\Services\DeveloperAppService as ApigeeXDeveloperAppService;
use Lordjoo\LaraApigee\Api\Edge\Edge;
use Lordjoo\LaraApigee\Api\Edge\Services\DeveloperAppService as EdgeDeveloperAppService;
use Lordjoo\LaraApigee\Facades\LaraApigee;

trait InteractsWithApigeeClient
{
    /**
     * Get the client based on the platform
     *
     * @return ApigeeX|Edge
     * @throws \Exception
     */
    protected function getClient(): ApigeeX|Edge
    {
        $platform = $this->getPlatform();
        if ($platform === 'edge') {
            return LaraApigee::edge();
        } elseif ($platform === 'apigeex') {
            return LaraApigee::apigeex();
        }
        throw new \Exception('Invalid platform');
    }

    /**
     * @return string|null
     */
    protected function getPlatform(): string|null
    {
        return config('ninjaportal.apigee_platform');
    }


}
