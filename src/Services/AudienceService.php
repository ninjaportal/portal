<?php

namespace NinjaPortal\Portal\Services;

use NinjaPortal\Portal\Contracts\Services\AudienceServiceInterface;
use NinjaPortal\Portal\Utils;

class AudienceService extends BaseService implements AudienceServiceInterface
{
    public static function getModel(): string
    {
        return Utils::getAudienceModel();
    }

}
