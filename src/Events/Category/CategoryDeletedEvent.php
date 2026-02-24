<?php

namespace NinjaPortal\Portal\Events\Category;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NinjaPortal\Portal\Models\Category;

class CategoryDeletedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public Category $category) {}
}

