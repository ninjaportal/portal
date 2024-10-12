<?php

namespace NinjaPortal\Portal\Seeders;

use Illuminate\Database\Seeder;
use NinjaPortal\Portal\Constants;
use NinjaPortal\Portal\Models\Setting;
use NinjaPortal\Portal\Models\SettingGroup;

class SettingsSeeder extends Seeder
{

    public function run(): void
    {
        $settings = [
            "Apigee" => [
                [
                    'key' => 'laraapigee.endpoint',
                    'label' => 'Apigee Endpoint',
                    'type' => Constants::SETTING_TYPES['string'],
                ],
                [
                    'key' => 'laraapigee.username',
                    'label' => 'Apigee Username',
                    'type' => Constants::SETTING_TYPES['string'],
                ],
                [
                    'key' => 'laraapigee.password',
                    'label' => 'Apigee Password',
                    'type' => Constants::SETTING_TYPES['string'],
                ],
                [
                    'key' => 'laraapigee.organization',
                    'label' => 'Apigee Organization',
                    'type' => Constants::SETTING_TYPES['string'],
                ],
                [
                    'key' => 'laraapigee.environment',
                    'label' => 'Apigee Environment',
                    'type' => Constants::SETTING_TYPES['string'],
                ]
            ]
        ];

        foreach ($settings as $group => $groupSettings) {
            $settingGroup = SettingGroup::create([
                'name' => $group,
            ]);
            foreach ($groupSettings as $setting) {
                Setting::updateOrCreate([
                    'setting_group_id' => $settingGroup->id,
                    'key' => $setting['key'],
                    'label' => $setting['label'],
                    'type' => $setting['type'],
                ]);
            }
        }
    }

}
