<?php

namespace NinjaPortal\Portal\Contracts\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Settings management contract.
 */
interface SettingServiceInterface extends ServiceInterface
{
    /**
     * Hydrate configuration values from the persistent store.
     */
    public function loadAllSettings(): void;

    /**
     * Retrieve a setting's value by key with appropriate type casting.
     */
    public function get(string $key): mixed;

    /**
     * Persist or update a setting value.
     */
    public function set(string $key, string $value, string $type): void;

    /**
     * Remove a setting entry.
     */
    public function delete(string|int $id): void;

    /**
     * Fetch all settings.
     */
    public function all(): Collection|array;

    /**
     * Expose a query builder for the settings model.
     */
    public function query(): Builder;
}
