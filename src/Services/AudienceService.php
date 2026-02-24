<?php

namespace NinjaPortal\Portal\Services;

use NinjaPortal\Portal\Contracts\Repositories\AudienceRepositoryInterface;
use NinjaPortal\Portal\Contracts\Services\AudienceServiceInterface;
use NinjaPortal\Portal\Services\Traits\CrudOperationsTrait;

/**
 * @mixin Traits\HasRepositoryAwareTrait<\NinjaPortal\Portal\Models\Audience, AudienceRepositoryInterface>
 */
class AudienceService extends BaseService implements AudienceServiceInterface
{
    use CrudOperationsTrait;

    public function __construct(AudienceRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }
}
