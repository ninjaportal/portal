<?php

namespace NinjaPortal\Portal\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use NinjaPortal\Portal\Contracts\Repositories\ApiProductRepositoryInterface;
use NinjaPortal\Portal\Contracts\Repositories\UserRepositoryInterface;
use NinjaPortal\Portal\Contracts\Services\AdminDashboardServiceInterface;
use NinjaPortal\Portal\Contracts\Services\ApiProductServiceInterface;

class AdminDashboardService implements AdminDashboardServiceInterface
{
    public function __construct(
        protected UserRepositoryInterface $users,
        protected ApiProductRepositoryInterface $apiProducts,
        protected ApiProductServiceInterface $apigeeProducts,
    ) {}

    public function summary(): array
    {
        $totalDevelopers = (int) $this->users->getBuilder()->count();
        $totalPortalProducts = (int) $this->apiProducts->getBuilder()->count();

        $linkedProductIds = $this->apiProducts->getBuilder()
            ->whereNotNull('apigee_product_id')
            ->where('apigee_product_id', '!=', '')
            ->pluck('apigee_product_id')
            ->map(static fn (mixed $value) => trim((string) $value))
            ->filter()
            ->unique()
            ->values();

        $apigeeSummary = $this->resolveApigeeSummary($linkedProductIds);

        return [
            'total_developers' => $totalDevelopers,
            'total_portal_products' => $totalPortalProducts,
            'linked_portal_products' => $linkedProductIds->count(),
            'total_apigee_products' => $apigeeSummary['total_apigee_products'],
            'total_unlisted_apigee_products' => $apigeeSummary['total_unlisted_apigee_products'],
            'catalog_link_coverage_percent' => $apigeeSummary['catalog_link_coverage_percent'],
            'apigee_available' => $apigeeSummary['apigee_available'],
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * @param  Collection<int, string>  $linkedProductIds
     * @return array{
     *     total_apigee_products:int|null,
     *     total_unlisted_apigee_products:int|null,
     *     catalog_link_coverage_percent:int|null,
     *     apigee_available:bool
     * }
     */
    protected function resolveApigeeSummary(Collection $linkedProductIds): array
    {
        $ttl = (int) config('ninjaportal.dashboard.apigee_summary_ttl_seconds', 60);
        $cacheKey = 'portal.dashboard.apigee-summary:'.md5($linkedProductIds->implode('|'));

        if ($ttl > 0) {
            return Cache::remember($cacheKey, $ttl, fn (): array => $this->buildApigeeSummary($linkedProductIds));
        }

        return $this->buildApigeeSummary($linkedProductIds);
    }

    /**
     * @param  Collection<int, string>  $linkedProductIds
     * @return array{
     *     total_apigee_products:int|null,
     *     total_linked_apigee_products:int|null,
     *     total_unlisted_apigee_products:int|null,
     *     catalog_link_coverage_percent:int|null,
     *     apigee_available:bool
     * }
     */
    protected function buildApigeeSummary(Collection $linkedProductIds): array
    {
        try {
            $apigeeProductIds = $this->apigeeProducts->apigeeProducts()
                ->map(fn (mixed $product) => $this->resolveApigeeProductIdentifier($product))
                ->filter()
                ->unique()
                ->values();

            $totalApigeeProducts = $apigeeProductIds->count();
            $unlistedProducts = $apigeeProductIds
                ->reject(fn (string $productId) => $linkedProductIds->contains($productId))
                ->count();
            return [
                'total_apigee_products' => $totalApigeeProducts,
                'total_unlisted_apigee_products' => $unlistedProducts,
                'catalog_link_coverage_percent' => $totalApigeeProducts > 0
                    ? (int) round((($totalApigeeProducts - $unlistedProducts) / $totalApigeeProducts) * 100)
                    : null,
                'apigee_available' => true,
            ];
        } catch (\Throwable) {
            return [
                'total_apigee_products' => null,
                'total_unlisted_apigee_products' => null,
                'catalog_link_coverage_percent' => null,
                'apigee_available' => false,
            ];
        }
    }

    protected function resolveApigeeProductIdentifier(mixed $product): ?string
    {
        $identifier = null;

        if (is_object($product) && method_exists($product, 'getName')) {
            $identifier = $product->getName();
        }

        if (! is_string($identifier) || trim($identifier) === '') {
            $identifier = data_get($product, 'name') ?? data_get($product, 'id') ?? data_get($product, 'apiproduct');
        }

        if (! is_string($identifier)) {
            return null;
        }

        $identifier = trim($identifier);

        return $identifier === '' ? null : $identifier;
    }
}
