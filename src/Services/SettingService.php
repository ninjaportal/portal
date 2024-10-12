<?php

namespace NinjaPortal\Portal\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use NinjaPortal\Portal\Models\Setting;

class SettingService implements IService
{
    public static function loadAllSettings(): void
    {
        if (!self::isCachable()) {
            self::loadSettingsFromDatabase();
            return;
        }

        $settings = Cache::get(self::getCacheKey());

        if (!$settings) {
            $settings = Setting::all()->keyBy('key');
            Cache::put(self::getCacheKey(), $settings, self::getTtl());
        }

        $settings->each(fn($setting) => self::setConfig($setting->key, $setting->value, $setting->type));
    }

    public static function get(string $key): ?string
    {
        if (self::isCachable()) {
            $cachedSettings = Cache::get(self::getCacheKey(), []);
            $setting = $cachedSettings[$key] ?? self::fetchSettingFromDatabase($key);

            return $setting ? self::castConfigValue($setting['value'], $setting['type']) : null;
        }

        $setting = Setting::where('key', $key)->first();
        return $setting ? self::castConfigValue($setting->value, $setting->type) : null;
    }

    public static function set(string $key, string $value, string $type): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value, 'type' => $type]);

        if (self::isCachable()) {
            $settings = Cache::get(self::getCacheKey(), []);
            $settings[$key] = compact('value', 'type');
            Cache::put(self::getCacheKey(), $settings, self::getTtl());
        }
    }

    public static function delete(string $key): void
    {
        Setting::where('key', $key)->delete();
        Config::set($key, null);

        if (self::isCachable()) {
            $settings = Cache::get(self::getCacheKey(), []);
            unset($settings[$key]);
            Cache::put(self::getCacheKey(), $settings, self::getTtl());
        }
    }

    public static function all(): Collection|array
    {
        return Setting::all();
    }

    public static function query(): Builder
    {
        return Setting::query();
    }

    protected static function getCacheKey(): string
    {
        return Config::get('ninjaportal.settings.cache.cache_key', 'portal_settings');
    }

    protected static function isCachable(): bool
    {
        return Config::get('ninjaportal.settings.cache.enabled', false);
    }

    protected static function getTtl(): int
    {
        return Config::get('ninjaportal.settings.cache.ttl', 3600);
    }

    protected static function setConfig(string $key, ?string $value, string $type): void
    {
        if (!is_null($value)) {
            Config::set($key, self::castConfigValue($value, $type));
        }
    }

    private static function castConfigValue(string $value, string $type): mixed
    {
        return match ($type) {
            'integer' => (int) $value,
            'boolean' => $value === 'true',
            'json' => json_decode($value, true),
            'float' => (float) $value,
            default => $value,
        };
    }

    private static function fetchSettingFromDatabase(string $key): ?array
    {
        $setting = Setting::where('key', $key)->first();
        if ($setting && self::isCachable()) {
            $cachedSettings = Cache::get(self::getCacheKey(), []);
            $cachedSettings[$key] = $setting;
            Cache::put(self::getCacheKey(), $cachedSettings, self::getTtl());
        }
        return $setting ? ['value' => $setting->value, 'type' => $setting->type] : null;
    }

    private static function loadSettingsFromDatabase(): void
    {
        Setting::all()->each(fn($setting) => self::setConfig($setting->key, $setting->value, $setting->type));
    }
}
