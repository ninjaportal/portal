<?php

namespace NinjaPortal\Portal\Contracts\Repositories;

use NinjaPortal\Portal\Common\Contracts\RepositoryInterface;
use NinjaPortal\Portal\Models\Setting;

/**
 * @extends RepositoryInterface<Setting>
 */
interface SettingRepositoryInterface extends RepositoryInterface
{
    public function findByKey(string $key): ?Setting;

    public function updateCacheable(string $key, string $value, string $type): void;

    public function updateOrCreateByKey(string $key, string $value, string $type): void;

    public function deleteByKey(string $key): void;
}
