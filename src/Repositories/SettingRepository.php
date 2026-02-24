<?php

namespace NinjaPortal\Portal\Repositories;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use NinjaPortal\Portal\Common\Repositories\BaseRepository;
use NinjaPortal\Portal\Contracts\Repositories\SettingRepositoryInterface;
use NinjaPortal\Portal\Models\Setting;

/**
 * @extends BaseRepository<Setting>
 */
class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    public function findByKey(string $key): ?Setting
    {
        $setting = $this->getBuilder()->where('key', $key)->first();

        if (! $setting) {
            return null;
        }

        return $setting;
    }

    public function updateCacheable(string $key, string $value, string $type): void
    {
        if (! $this->isCacheEnabled()) {
            return;
        }

        $settings = Cache::get($this->getCacheKey(), []);
        $settings[$key] = compact('value', 'type');
        Cache::put($this->getCacheKey(), $settings, $this->getTtl());
    }

    public function updateOrCreateByKey(string $key, string $value, string $type): void
    {
        $this->getBuilder()->updateOrCreate(['key' => $key], ['value' => $value, 'type' => $type]);
    }

    public function deleteByKey(string $key): void
    {
        $this->getBuilder()->where('key', $key)->delete();
    }

    protected function isCacheEnabled(): bool
    {
        return Config::get('ninjaportal.settings.cache.enabled', false);
    }

    protected function getCacheKey(): string
    {
        return Config::get('ninjaportal.settings.cache.key', 'portal.settings');
    }

    protected function getTtl(): int
    {
        return Config::get('ninjaportal.settings.cache.ttl', 3600);
    }
}
