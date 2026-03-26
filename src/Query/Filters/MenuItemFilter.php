<?php

namespace NinjaPortal\Portal\Query\Filters;

use NinjaPortal\Portal\Common\Query\Filters\FilterAbstract;

class MenuItemFilter extends FilterAbstract
{
    protected array $filters = [
        'menu_id' => 'menu_id',
        'slug' => 'slug',
    ];
}
