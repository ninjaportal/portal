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
            'Portal' => [
                [
                    'key' => 'portal.name',
                    'label' => 'Portal Name',
                    'type' => Constants::SETTING_TYPES['string'],
                    'value' => 'Ninja API Portal',
                ],
                [
                    'key' => 'portal.supported_locales',
                    'label' => 'Supported Languages',
                    'type' => Constants::SETTING_TYPES['string'],
                    'value' => 'ar,en',
                ],
                [
                    'key' => 'portal.tagline',
                    'label' => 'Portal Tagline',
                    'type' => Constants::SETTING_TYPES['string'],
                    'value' => 'Launch integrations faster with curated APIs.',
                ],
                [
                    'key' => 'portal.support_email',
                    'label' => 'Support Email',
                    'type' => Constants::SETTING_TYPES['string'],
                    'value' => 'support@ninjaportal.test',
                ],
            ],
            'Branding' => [
                [
                    'key' => 'branding.primary_color',
                    'label' => 'Primary Color',
                    'type' => Constants::SETTING_TYPES['string'],
                    'value' => '#1F2937',
                ],
                [
                    'key' => 'branding.secondary_color',
                    'label' => 'Secondary Color',
                    'type' => Constants::SETTING_TYPES['string'],
                    'value' => '#0EA5E9',
                ],
                [
                    'key' => 'branding.logo_url',
                    'label' => 'Logo',
                    'type' => Constants::SETTING_TYPES['string'],
                    'value' => 'https://placehold.co/160x40?text=Ninja+Portal',
                ],
                [
                    'key' => 'branding.show_logo_text',
                    'label' => 'Show App Name Beside Logo',
                    'type' => Constants::SETTING_TYPES['boolean'],
                    'value' => '0',
                ],
                [
                    'key' => 'branding.favicon_url',
                    'label' => 'Fav Icon',
                    'type' => Constants::SETTING_TYPES['string'],
                    'value' => 'https://placehold.co/32x32?text=NP',
                ],
            ],
            'Feature Flags' => [
                [
                    'key' => 'features.show_demo_banner',
                    'label' => 'Show Demo Banner',
                    'type' => Constants::SETTING_TYPES['boolean'],
                    'value' => '1',
                ],
                [
                    'key' => 'features.enable_self_service_keys',
                    'label' => 'Enable Self-Service API Keys',
                    'type' => Constants::SETTING_TYPES['boolean'],
                    'value' => '1',
                ],
                [
                    'key' => 'features.allow_unapproved_app_creation',
                    'label' => 'Allow Unapproved Users to Create Apps',
                    'type' => Constants::SETTING_TYPES['boolean'],
                    'value' => '0',
                ],
            ],
            'Apigee' => [
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
                ],
            ],
        ];

        foreach ($settings as $group => $groupSettings) {
            $settingGroup = SettingGroup::updateOrCreate([
                'name' => $group,
            ]);
            foreach ($groupSettings as $setting) {
                Setting::updateOrCreate(
                    [
                        'setting_group_id' => $settingGroup->id,
                        'key' => $setting['key'],
                    ],
                    [
                        'label' => $setting['label'],
                        'type' => $setting['type'],
                        'value' => $setting['value'] ?? null,
                    ]
                );
            }
        }
    }
}
