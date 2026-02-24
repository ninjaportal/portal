<?php

namespace NinjaPortal\Portal\Common\Query\Search;

use Illuminate\Database\Eloquent\Builder;

interface AttributeSearchTypeInterface
{
    public function search(Builder $builder, string $value): Builder;
}

