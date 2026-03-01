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

    public static function visibilities(): array
    {
        return [
            'public',
            'private',
            'draft',
        ];
    }

    public static function defaultVisibility(): string
    {
        $configured = strtolower(trim((string) config('ninjaportal.api_products.default_visibility', 'public')));

        return in_array($configured, static::visibilities(), true)
            ? $configured
            : 'public';
    }

    public static function storageDisk(): string
    {
        $disk = trim((string) config('ninjaportal.api_products.storage_disk', 'public'));

        return $disk !== '' ? $disk : 'public';
    }
}
