<?php

namespace NinjaPortal\Portal\Models;

use Illuminate\Database\Eloquent\Model;

class ApiProductTranslation extends Model
{
    protected $fillable = [
        'name',
        'description',
        'short_description',
        'thumbnail',
    ];

}
