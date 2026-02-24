<?php

namespace NinjaPortal\Portal\Common\Query\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class CreatedAtFromFilterAttr implements AttributeFilterInterface
{
    public function filter(Builder $builder, mixed $value): Builder
    {
        try {
            $date = Carbon::parse($value)->format('Y-m-d');

            return $builder->whereDate('created_at', '>=', $date);
        } catch (\Throwable) {
            return $builder;
        }
    }
}

