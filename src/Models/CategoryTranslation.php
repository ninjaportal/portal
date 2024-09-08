<?php

namespace NinjaPortal\Portal\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryTranslation extends Model
{
    protected $fillable = [
        "name",
        "description",
        "short_description",
        "locale",
        "category_id"
    ];
}
