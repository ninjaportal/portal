<?php

namespace NinjaPortal\Portal\Observers;

use NinjaPortal\Admin\Models\Setting;
use NinjaPortal\Portal\Services\SettingService;

class SettingObserver
{
    public function created(Setting $model)
    {
        SettingService::set($model->key, $model->value, $model->type);
    }

    public function updated(Setting $model)
    {
        SettingService::set($model->key, $model->value, $model->type);
    }

    public function deleted(Setting $model)
    {
        SettingService::delete($model->key);
    }

}
