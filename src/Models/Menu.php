<?php

namespace NinjaPortal\Portal\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use NinjaPortal\Portal\Query\Filters\MenuFilter;
use NinjaPortal\Portal\Query\Search\MenuSearch;

class Menu extends Model
{
    protected $fillable = [
        'slug',
    ];

    protected $relationships = [
        'items',
    ];

    public static function slug($slug): ?Menu
    {
        return self::where('slug', $slug)->first();
    }

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function scopeSearch(Builder $builder): Builder
    {
        return (new MenuSearch)->apply($builder);
    }

    public function scopeFilter(Builder $builder): Builder
    {
        return (new MenuFilter)->apply($builder);
    }
}
