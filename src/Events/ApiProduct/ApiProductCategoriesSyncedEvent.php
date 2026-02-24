<?php

namespace NinjaPortal\Portal\Events\ApiProduct;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NinjaPortal\Portal\Models\ApiProduct;

class ApiProductCategoriesSyncedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @param  array<int, int|string>  $categoryIds
     */
    public function __construct(public ApiProduct $apiProduct, public array $categoryIds) {}
}

