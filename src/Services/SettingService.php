<?php

namespace NinjaPortal\Portal\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use NinjaPortal\Portal\Contracts\Repositories\SettingRepositoryInterface;
use NinjaPortal\Portal\Contracts\Services\SettingServiceInterface;
use NinjaPortal\Portal\Services\Traits\CrudOperationsTrait;

/**
 * @mixin Traits\HasRepositoryAwareTrait<\NinjaPortal\Portal\Models\Setting, SettingRepositoryInterface>
 */
class SettingService extends BaseService implements SettingServiceInterface
{
    use CrudOperationsTrait {
        delete as parentDelete;
    }

    public function __construct(SettingRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function loadAllSettings(): void
    {
        if (! $this->isCachable()) {
            $this->loadSettingsFromDatabase();

            return;
        }

        $settings = Cache::get($this->getCacheKey());

        if (! $settings) {
            $settings = $this->repository()
                ->list()
                ->mapWithKeys(fn ($setting) => [
                    $setting->key => [
                        'value' => $setting->value,
                        'type' => $setting->type,
                    ],
                ])->toArray();

            Cache::put($this->getCacheKey(), $settings, $this->getTtl());
        }

        foreach ($settings as $key => $setting) {
            $this->setConfig($key, $setting['value'] ?? null, $setting['type'] ?? 'string');
        }
    }

    public function get(string $key): mixed
    {
        if ($this->isCachable()) {
            $cachedSettings = Cache::get($this->getCacheKey(), []);

            if (! isset($cachedSettings[$key])) {
                $setting = $this->repository()->findByKey($key);
                if (! $setting) {
                    return null;
                }

                $cachedSettings[$key] = [
                    'value' => $setting->value,
                    'type' => $setting->type,
                ];

                Cache::put($this->getCacheKey(), $cachedSettings, $this->getTtl());
            }

            return $this->castConfigValue($cachedSettings[$key]['value'] ?? null, $cachedSettings[$key]['type'] ?? 'string');
        }

        $setting = $this->repository()->findByKey($key);

        if (! $setting) {
            return null;
        }

        return $this->castConfigValue($setting->value, $setting->type);
    }

    public function set(string $key, string $value, string $type): void
    {
        $this->repository()->getBuilder()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );

        if ($this->isCachable()) {
            $this->repository()->updateCacheable($key, $value, $type);
        }
    }

    public function delete(int|string $id): void
    {
        $setting = is_int($id)
            ? $this->repository()->find($id)
            : $this->repository()->findByKey($id);
        if (! $setting) {
            return;
        }
        $this->parentDelete($setting->id);
        if ($this->isCachable()) {
            $cachedSettings = Cache::get($this->getCacheKey(), []);
            if (isset($cachedSettings[$setting->key])) {
                unset($cachedSettings[$setting->key]);
                Cache::put($this->getCacheKey(), $cachedSettings, $this->getTtl());
            }
        }
    }

    public function all(): Collection|array
    {
        return $this->repository()->list();
    }

    public function query(): Builder
    {
        return $this->repository()->getBuilder();
    }

    protected function getCacheKey(): string
    {
        return Config::get('ninjaportal.settings.cache.key', 'portal.settings');
    }

    protected function isCachable(): bool
    {
        return Config::get('ninjaportal.settings.cache.enabled', false);
    }

    protected function getTtl(): int
    {
        return Config::get('ninjaportal.settings.cache.ttl', 3600);
    }

    protected function setConfig(string $key, ?string $value, string $type): void
    {
        if (! is_null($value)) {
            Config::set($key, $this->castConfigValue($value, $type));
        }
    }

    private function castConfigValue(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'integer' => (int) $value,
            'boolean' => $this->toBool($value),
            'json' => json_decode($value, true),
            'float' => (float) $value,
            default => $value,
        };
    }

    private function toBool(string $value): bool
    {
        $normalized = strtolower(trim($value));

        if (in_array($normalized, ['1', 'true', 'yes', 'on'], true)) {
            return true;
        }

        if (in_array($normalized, ['0', 'false', 'no', 'off', ''], true)) {
            return false;
        }

        return (bool) $normalized;
    }

    private function loadSettingsFromDatabase(): void
    {
        $this->repository()
            ->list()
            ->each(fn ($setting) => $this->setConfig($setting->key, $setting->value, $setting->type));
    }
}
