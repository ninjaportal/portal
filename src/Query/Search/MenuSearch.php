<?php

namespace NinjaPortal\Portal\Query\Search;

use NinjaPortal\Portal\Common\Query\Search\SearchAbstract;

class MenuSearch extends SearchAbstract
{
    protected array $search = [
        'slug',
    ];
}
