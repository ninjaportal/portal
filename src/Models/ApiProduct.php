<?php

namespace NinjaPortal\Portal\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use NinjaPortal\Portal\Query\Filters\ApiProductFilter;
use NinjaPortal\Portal\Query\Search\ApiProductSearch;
use NinjaPortal\Portal\Translatable\HasTranslations;

class ApiProduct extends Model
{
    use HasTranslations;

    /**
     * used for storing the swagger file in the storage
     */
    public static string $STORAGE_DISK = 'public';

    public static array $VISIBILITY = [
        'public' => 'public',
        'private' => 'private',
        'draft' => 'draft',
    ];

    protected $fillable = [
        'slug',
        'swagger_url',
        'integration_file',
        'apigee_product_id',
        'visibility',
        'tags',
        'custom_attributes',
    ];

    protected $casts = [
        'tags' => 'array',
        'custom_attributes' => 'array',
    ];

    public $translated_attributes = ['name', 'description', 'short_description', 'thumbnail'];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function audiences(): BelongsToMany
    {
        return $this->belongsToMany(Audience::class);
    }

    public function scopeSearch(Builder $builder): Builder
    {
        return (new ApiProductSearch)->apply($builder);
    }

    public function scopeFilter(Builder $builder): Builder
    {
        return (new ApiProductFilter)->apply($builder);
    }
}
