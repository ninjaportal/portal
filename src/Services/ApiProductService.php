<?php

namespace NinjaPortal\Portal\Services;

use Illuminate\Support\Collection;
use Lordjoo\LaraApigee\Api\ApigeeX\Entities\ApiProduct;
use NinjaPortal\Portal\Contracts\Repositories\ApiProductRepositoryInterface;
use NinjaPortal\Portal\Contracts\Services\ApiProductServiceInterface;
use NinjaPortal\Portal\Events\ApiProduct\ApiProductAudiencesSyncedEvent;
use NinjaPortal\Portal\Events\ApiProduct\ApiProductCategoriesSyncedEvent;
use NinjaPortal\Portal\Models\ApiProduct as PortalApiProduct;
use NinjaPortal\Portal\Services\Traits\CrudOperationsTrait;
use NinjaPortal\Portal\Utils;

/**
 * @mixin Traits\HasRepositoryAwareTrait<\NinjaPortal\Portal\Models\ApiProduct, ApiProductRepositoryInterface>
 */
class ApiProductService extends BaseService implements ApiProductServiceInterface
{
    use CrudOperationsTrait;

    public function __construct(ApiProductRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function public(): Collection
    {
        return $this->repository()->listPublic();
    }

    public function private(): Collection
    {
        return $this->repository()->listPrivate();
    }

    public function mine(): Collection
    {
        $userAudienceIds = $this->getUserAudience()->pluck('id')->values()->all();

        return $this->repository()->listAvailableForAudienceIds($userAudienceIds);
    }

    protected function getUserAudience()
    {
        $user = auth()->user();

        return $user->audiences ?? collect();
    }

    /**
     * Get the query builder for API products.
     *
     * @return Collection<\Lordjoo\LaraApigee\Api\Edge\Entities\ApiProduct>| Collection<ApiProduct>
     */
    public function apigeeProducts(): Collection
    {
        return Utils::getApigeeClient()->apiProducts()->get();
    }

    public function syncCategories(PortalApiProduct|int|string $apiProduct, array $categoryIds): PortalApiProduct
    {
        $apiProduct = $this->repository()->resolve($apiProduct);

        $this->callHook('beforeSyncCategories', [$apiProduct, $categoryIds]);

        $apiProduct->categories()->sync($categoryIds);
        $apiProduct->load('categories');

        $this->callHook('afterSyncCategories', [$apiProduct, $categoryIds]);

        ApiProductCategoriesSyncedEvent::dispatch($apiProduct, $categoryIds);

        return $apiProduct;
    }

    public function syncAudiences(PortalApiProduct|int|string $apiProduct, array $audienceIds): PortalApiProduct
    {
        $apiProduct = $this->repository()->resolve($apiProduct);

        $this->callHook('beforeSyncAudiences', [$apiProduct, $audienceIds]);

        $apiProduct->audiences()->sync($audienceIds);
        $apiProduct->load('audiences');

        $this->callHook('afterSyncAudiences', [$apiProduct, $audienceIds]);

        ApiProductAudiencesSyncedEvent::dispatch($apiProduct, $audienceIds);

        return $apiProduct;
    }
}
