<?php

namespace NinjaPortal\Portal\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use NinjaPortal\Portal\Query\Filters\AudienceFilter;
use NinjaPortal\Portal\Query\Search\AudienceSearch;

class Audience extends Model
{
    protected $fillable = [
        'name',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(ApiProduct::class);
    }

    public function scopeSearch(Builder $builder): Builder
    {
        return (new AudienceSearch)->apply($builder);
    }

    public function scopeFilter(Builder $builder): Builder
    {
        return (new AudienceFilter)->apply($builder);
    }
}
