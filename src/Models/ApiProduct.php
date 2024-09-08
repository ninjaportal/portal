<?php

namespace NinjaPortal\Portal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use NinjaPortal\Portal\Translatable\HasTranslations;

class ApiProduct extends Model
{
    use HasTranslations;

    /**
     * used for storing the swagger file in the storage
     * @var string  $STORAGE_DISK
     */
    public static string $STORAGE_DISK = 'public';

    public static array $VISIBILITY = [
        'public' => 'public',
        'private' => 'private',
    ];

    protected $fillable = ['slug', 'swagger_url', 'apigee_product_id','visibility'];
    public $translated_attributes = ['name', 'description', 'short_description', 'thumbnail'];


    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function audiences(): BelongsToMany
    {
        return $this->belongsToMany(Audience::class);
    }
}
