<?php

namespace NinjaPortal\Portal\Query\Filters;

use NinjaPortal\Portal\Common\Query\Filters\FilterAbstract;

class MenuFilter extends FilterAbstract
{
    protected array $filters = [
        'slug' => 'slug',
    ];
}
