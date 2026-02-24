<?php

namespace NinjaPortal\Portal\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use NinjaPortal\Portal\Common\Contracts\RepositoryInterface;
use NinjaPortal\Portal\Models\ApiProduct;

/**
 * @extends RepositoryInterface<ApiProduct>
 */
interface ApiProductRepositoryInterface extends RepositoryInterface
{
    /**
     * @return Collection<int, ApiProduct>
     */
    public function listPublic(): Collection;

    /**
     * @return Collection<int, ApiProduct>
     */
    public function listPrivate(): Collection;

    /**
     * @param  array<int, int|string>  $audienceIds
     * @return Collection<int, ApiProduct>
     */
    public function listAvailableForAudienceIds(array $audienceIds): Collection;
}
