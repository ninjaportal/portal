<?php

namespace NinjaPortal\Portal\Models;

use Illuminate\Database\Eloquent\Model;
use NinjaPortal\Portal\Translatable\HasTranslations;

class MenuItem extends Model
{

    use HasTranslations;

    protected $fillable = [
        'slug',
        'menu_id',
    ];

    public array $translated_attributes = [
        'title',
        'url',
        'route',
    ];

    protected $relations = ['menu_item_translations'];


}
