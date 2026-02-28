<?php

namespace NinjaPortal\Portal\Services;

use NinjaPortal\Portal\Contracts\Repositories\AudienceRepositoryInterface;
use NinjaPortal\Portal\Contracts\Services\AudienceServiceInterface;
use NinjaPortal\Portal\Events\Audience\AudienceProductsSyncedEvent;
use NinjaPortal\Portal\Events\Audience\AudienceUsersSyncedEvent;
use NinjaPortal\Portal\Models\Audience;
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

    public function syncUsers(Audience|int|string $audience, array $userIds): Audience
    {
        $audience = $this->repository()->resolve($audience);

        $this->callHook('beforeSyncUsers', [$audience, $userIds]);

        $audience->users()->sync($userIds);
        $audience->load('users');

        $this->callHook('afterSyncUsers', [$audience, $userIds]);

        AudienceUsersSyncedEvent::dispatch($audience, $userIds);

        return $audience;
    }

    public function syncProducts(Audience|int|string $audience, array $apiProductIds): Audience
    {
        $audience = $this->repository()->resolve($audience);

        $this->callHook('beforeSyncProducts', [$audience, $apiProductIds]);

        $audience->products()->sync($apiProductIds);
        $audience->load('products');

        $this->callHook('afterSyncProducts', [$audience, $apiProductIds]);

        AudienceProductsSyncedEvent::dispatch($audience, $apiProductIds);

        return $audience;
    }
}
