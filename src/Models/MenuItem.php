<?php

namespace NinjaPortal\Portal\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use NinjaPortal\Portal\Query\Filters\MenuItemFilter;
use NinjaPortal\Portal\Query\Search\MenuItemSearch;
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

    public function scopeSearch(Builder $builder): Builder
    {
        return (new MenuItemSearch)->apply($builder);
    }

    public function scopeFilter(Builder $builder): Builder
    {
        return (new MenuItemFilter)->apply($builder);
    }
}
