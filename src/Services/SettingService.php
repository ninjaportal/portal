<?php

namespace NinjaPortal\Portal\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use NinjaPortal\Portal\Contracts\Services\ServiceInterface;
use NinjaPortal\Portal\Utils;

class SettingService implements ServiceInterface
{
    /**
     * Load all settings into cache or from the database.
     */
    public static function loadAllSettings(): void
    {
        if (!self::isCachable()) {
            self::loadSettingsFromDatabase();
            return;
        }

        $settings = Cache::get(self::getCacheKey());

        if (!$settings) {
            $settings = self::getModel()::all()->keyBy('key');
            Cache::put(self::getCacheKey(), $settings, self::getTtl());
        }

        $settings->each(fn($setting) => self::setConfig($setting->key, $setting->value, $setting->type));
    }

    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @return string|null
     */
    public static function get(string $key): ?string
    {
        if (self::isCachable()) {
            $cachedSettings = Cache::get(self::getCacheKey(), []);
            $setting = $cachedSettings[$key] ?? self::fetchSettingFromDatabase($key);

            return $setting ? self::castConfigValue($setting['value'], $setting['type']) : null;
        }

        $setting = self::getModel()::where('key', $key)->first();
        return $setting ? self::castConfigValue($setting->value, $setting->type) : null;
    }

    /**
     * Set or update a setting.
     *
     * @param string $key
     * @param string $value
     * @param string $type
     */
    public static function set(string $key, string $value, string $type): void
    {
        self::getModel()::updateOrCreate(['key' => $key], ['value' => $value, 'type' => $type]);

        if (self::isCachable()) {
            $settings = Cache::get(self::getCacheKey(), []);
            $settings[$key] = compact('value', 'type');
            Cache::put(self::getCacheKey(), $settings, self::getTtl());
        }
    }

    /**
     * Delete a setting by key.
     *
     * @param string $key
     */
    public static function delete(string $key): void
    {
        self::getModel()::where('key', $key)->delete();
        Config::set($key, null);

        if (self::isCachable()) {
            $settings = Cache::get(self::getCacheKey(), []);
            unset($settings[$key]);
            Cache::put(self::getCacheKey(), $settings, self::getTtl());
        }
    }

    /**
     * Get all settings.
     *
     * @return Collection|array
     */
    public static function all(): Collection|array
    {
        return self::getModel()::all();
    }

    /**
     * Get the query builder for the Setting model.
     *
     * @return Builder
     */
    public static function query(): Builder
    {
        return self::getModel()::query();
    }

    /**
     * Retrieve the cache key for settings.
     *
     * @return string
     */
    protected static function getCacheKey(): string
    {
        return Config::get('ninjaportal.settings.cache.key', 'portal.settings');
    }

    /**
     * Determine if caching is enabled for settings.
     *
     * @return bool
     */
    protected static function isCachable(): bool
    {
        return Config::get('ninjaportal.settings.cache.enabled', false);
    }

    /**
     * Get the cache Time-To-Live (TTL) value.
     *
     * @return int
     */
    protected static function getTtl(): int
    {
        return Config::get('ninjaportal.settings.cache.ttl', 3600);
    }

    /**
     * Set a configuration value for the given key, value, and type.
     *
     * @param string $key
     * @param string|null $value
     * @param string $type
     */
    protected static function setConfig(string $key, ?string $value, string $type): void
    {
        if (!is_null($value)) {
            Config::set($key, self::castConfigValue($value, $type));
        }
    }

    /**
     * Cast a configuration value based on its type.
     *
     * @param string $value
     * @param string $type
     * @return mixed
     */
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

    /**
     * Fetch a setting from the database by key.
     *
     * @param string $key
     * @return array|null
     */
    private static function fetchSettingFromDatabase(string $key): ?array
    {
        $setting = self::getModel()::where('key', $key)->first();
        if ($setting && self::isCachable()) {
            $cachedSettings = Cache::get(self::getCacheKey(), []);
            $cachedSettings[$key] = $setting;
            Cache::put(self::getCacheKey(), $cachedSettings, self::getTtl());
        }
        return $setting ? ['value' => $setting->value, 'type' => $setting->type] : null;
    }

    /**
     * Load all settings from the database and apply them to the configuration.
     */
    private static function loadSettingsFromDatabase(): void
    {
        self::getModel()::all()->each(fn($setting) => self::setConfig($setting->key, $setting->value, $setting->type));
    }

    /**
     * Retrieve the model class for the Setting model dynamically.
     *
     * @return string|null
     */
    public static function getModel(): ?string
    {
        return Utils::getSettingModel();
    }
}
