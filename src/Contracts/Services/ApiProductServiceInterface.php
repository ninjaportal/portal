<?php

namespace NinjaPortal\Portal\Contracts\Services;

use Illuminate\Support\Collection;
use NinjaPortal\Portal\Models\ApiProduct;

/**
 * API product domain service contract.
 */
interface ApiProductServiceInterface extends ServiceInterface
{
    /**
     * Retrieve API products directly from Apigee.
     */
    public function apigeeProducts(): Collection;

    /**
     * Retrieve products visible to all audiences.
     */
    public function public(): Collection;

    /**
     * Retrieve private products.
     */
    public function private(): Collection;

    /**
     * Retrieve products associated with the authenticated user's audiences.
     */
    public function mine(): Collection;

    /**
     * @param  array<int, int|string>  $categoryIds
     */
    public function syncCategories(ApiProduct|int|string $apiProduct, array $categoryIds): ApiProduct;

    /**
     * @param  array<int, int|string>  $audienceIds
     */
    public function syncAudiences(ApiProduct|int|string $apiProduct, array $audienceIds): ApiProduct;
}
