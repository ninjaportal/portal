<?php

namespace NinjaPortal\Portal\Models;

use Illuminate\Database\Eloquent\Model;
use NinjaPortal\Portal\Translatable\HasTranslations;

class Menu extends Model
{
    use HasTranslations;

    protected $fillable = [
        'slug',
        'name'
    ];

    public $translated_attributes = [
        'content',
    ];


    public static function name($name): Menu|null
    {
        return self::where('name', $name)->first();
    }

}
