<?php

namespace NinjaPortal\Portal\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use NinjaPortal\Portal\Query\Filters\SettingFilter;
use NinjaPortal\Portal\Query\Search\SettingSearch;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'label', 'setting_group_id', 'type'];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        $setting = self::firstOrNew(['key' => $key]);
        $setting->value = $value;
        $setting->save();
    }

    public static function forget($key)
    {
        self::where('key', $key)->delete();
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(SettingGroup::class, 'setting_group_id');
    }

    public function scopeSearch(Builder $builder): Builder
    {
        return (new SettingSearch)->apply($builder);
    }

    public function scopeFilter(Builder $builder): Builder
    {
        return (new SettingFilter)->apply($builder);
    }
}
