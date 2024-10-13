<?php

namespace NinjaPortal\Portal\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItemTranslation extends Model
{
    protected $fillable = [
        'title',
        'url',
        'route',
        'locale',
        'menu_item_id',
    ];

}
