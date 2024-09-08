<?php

namespace NinjaPortal\Portal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use NinjaPortal\Portal\Translatable\HasTranslations;

class Category extends Model
{
    use HasTranslations;

    protected $fillable = ['slug'];

    public array $translated_attributes = ['name', 'description', 'short_description', 'thumbnail'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(ApiProduct::class);
    }

}
