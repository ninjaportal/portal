<?php

namespace NinjaPortal\Portal\Common\Query\Filters;

use Illuminate\Database\Eloquent\Builder;

interface AttributeFilterInterface
{
    public function filter(Builder $builder, mixed $value): Builder;
}

