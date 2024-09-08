<?php

namespace NinjaPortal\Portal\Models;

use Illuminate\Database\Eloquent\Model;

class MenuTranslation extends Model
{
    protected $fillable = [
        'content'
    ];

    protected $casts = [
        'content' => 'array'
    ];
}
