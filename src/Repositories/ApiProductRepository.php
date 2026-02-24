<?php

namespace NinjaPortal\Portal\Repositories;

use Illuminate\Database\Eloquent\Collection;
use NinjaPortal\Portal\Common\Repositories\BaseRepository;
use NinjaPortal\Portal\Contracts\Repositories\ApiProductRepositoryInterface;
use NinjaPortal\Portal\Models\ApiProduct;

/**
 * @extends BaseRepository<\NinjaPortal\Portal\Models\ApiProduct>
 */
class ApiProductRepository extends BaseRepository implements ApiProductRepositoryInterface
{
    /**
     * @return Collection<int, ApiProduct>
     */
    public function listPublic(): Collection
    {
        return $this->getBuilder()
            ->where('visibility', 'public')
            ->get();
    }

    /**
     * @return Collection<int, ApiProduct>
     */
    public function listPrivate(): Collection
    {
        return $this->getBuilder()
            ->where('visibility', 'private')
            ->get();
    }

    /**
     * @param  array<int, int|string>  $audienceIds
     * @return Collection<int, ApiProduct>
     */
    public function listAvailableForAudienceIds(array $audienceIds): Collection
    {
        return $this->getBuilder()
            ->where(function ($query) use ($audienceIds) {
                $query->whereHas('audiences', function ($query) use ($audienceIds) {
                    $query->whereIn('audience_id', $audienceIds);
                })->orWhereDoesntHave('audiences');
            })
            ->get();
    }
}
