<?php

namespace NinjaPortal\Portal\Contracts\Services;

use NinjaPortal\Portal\Models\Audience;

/**
 * Audience domain service contract.
 */
interface AudienceServiceInterface extends ServiceInterface
{
    /**
     * @param  array<int, int|string>  $userIds
     */
    public function syncUsers(Audience|int|string $audience, array $userIds): Audience;

    /**
     * @param  array<int, int|string>  $apiProductIds
     */
    public function syncProducts(Audience|int|string $audience, array $apiProductIds): Audience;
}
