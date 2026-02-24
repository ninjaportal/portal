<?php

namespace NinjaPortal\Portal\Query\Filters;

use Illuminate\Database\Eloquent\Builder;
use NinjaPortal\Portal\Common\Query\Filters\FilterAbstract;
use NinjaPortal\Portal\Models\ApiProduct;

class ApiProductFilter extends FilterAbstract
{
    protected array $filters = [
        'visibility' => 'filterVisibility',
    ];

    protected function filterVisibility(Builder $builder, mixed $value): void
    {
        $visibility = strtolower(trim((string) $value));
        if ($visibility === '' || ! array_key_exists($visibility, ApiProduct::$VISIBILITY)) {
            return;
        }

        $builder->where('visibility', $visibility);
    }
}

