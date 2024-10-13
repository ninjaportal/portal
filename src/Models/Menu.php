<?php

namespace NinjaPortal\Portal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{

    protected $fillable = [
        'slug'
    ];

    protected $relationships = [
        'items'
    ];

    public static function slug($slug): Menu|null
    {
        return self::where('slug', $slug)->first();
    }

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

}
