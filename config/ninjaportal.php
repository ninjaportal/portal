<?php

return [

    /*
     * the type of the platform
     * either "edge" or "apigee" are supported
     * apigee is used for ApigeeX
     *
     * this is used to determine the type of the platform
     */
    "apigee_platform" => "edge",

    'translatable' => [
        /*
         * The default translation model prefix {ModelName}Translation
         */
        'translation_suffix' => 'Translation',

        /*
         * The default translation model namespace
         */
        'translation_model_namespace' => null,
    ],

    'settings' => [
        'cache' => [
            /*
             * Enable or disable the cache for the settings
             */
            'enabled' => true,

            /*
             * The cache key for the settings
             */
            'key' => 'portal.settings',

            /*
             * The cache ttl for the settings
             */
            'ttl' => 60 * 60,
        ],
    ],


    'translations' => [
        /*
         * Enable or disable the fallback locale
         */
        'with_fallback' => true,

        /*
         * The fallback locale
         */
        'fallback_locale' => 'en',
    ],

    'models' => [
        'Admin' => \NinjaPortal\Portal\Models\Admin::class,
        'ApiProduct' => \NinjaPortal\Portal\Models\ApiProduct::class,
        'Audience' => \NinjaPortal\Portal\Models\Audience::class,
        'Category' => \NinjaPortal\Portal\Models\Category::class,
        'Menu' => \NinjaPortal\Portal\Models\Menu::class,
        'MenuItem' => \NinjaPortal\Portal\Models\MenuItem::class,
        'Setting' => \NinjaPortal\Portal\Models\Setting::class,
        'SettingGroup' => \NinjaPortal\Portal\Models\SettingGroup::class,
        'User' => \NinjaPortal\Portal\Models\User::class,
    ]


];
