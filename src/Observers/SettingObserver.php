<?php

namespace NinjaPortal\Portal\Observers;

use NinjaPortal\Portal\Contracts\Services\SettingServiceInterface;
use NinjaPortal\Portal\Models\Setting;

class SettingObserver
{
    public function __construct(protected SettingServiceInterface $settings) {}

    public function created(Setting $model): void
    {
        $this->settings->set($model->key, $model->value, $model->type);
    }

    public function updated(Setting $model): void
    {
        $this->settings->set($model->key, $model->value, $model->type);
    }

    public function deleted(Setting $model): void
    {
        $this->settings->delete($model->key);
    }
}
