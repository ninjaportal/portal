<?php

namespace NinjaPortal\Portal\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use NinjaPortal\Portal\Translatable\HasTranslations;
use NinjaPortal\Portal\Query\Filters\CategoryFilter;
use NinjaPortal\Portal\Query\Search\CategorySearch;

class Category extends Model
{
    use HasTranslations;

    protected $fillable = ['slug'];

    public array $translated_attributes = ['name', 'description', 'short_description', 'thumbnail'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(ApiProduct::class);
    }

    public function scopeSearch(Builder $builder): Builder
    {
        return (new CategorySearch)->apply($builder);
    }

    public function scopeFilter(Builder $builder): Builder
    {
        return (new CategoryFilter)->apply($builder);
    }
}
