<?php

namespace NinjaPortal\Portal\Contracts\Services;

/**
 * Admin dashboard summary service contract.
 */
interface AdminDashboardServiceInterface
{
    /**
     * Build the admin dashboard summary payload.
     *
     * @return array{
     *     total_developers:int,
     *     total_portal_products:int,
     *     linked_portal_products:int,
     *     total_apigee_products:int|null,
     *     total_unlisted_apigee_products:int|null,
     *     catalog_link_coverage_percent:int|null,
     *     apigee_available:bool,
     *     generated_at:string
     * }
     */
    public function summary(): array;
}
