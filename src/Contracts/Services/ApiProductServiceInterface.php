<?php

namespace NinjaPortal\Portal\Contracts\Services;

use Illuminate\Support\Collection;

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
}
