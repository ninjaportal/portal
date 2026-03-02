<?php

return [

    /*
     * the type of the platform
     * either "edge" or "apigeex" are supported
     * apigeex is used for ApigeeX
     *
     * this is used to determine the type of the platform
     */
    'apigee_platform' => env('APIGEE_PLATFORM', 'edge'),

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

    /*
     * Backwards/forwards-compatible translation config.
     *
     * The translatable system in `NinjaPortal\\Portal\\Translatable\\HasTranslations`
     * reads from `ninjaportal.translatable.*`, while older config used
     * `ninjaportal.translations` + `ninjaportal.locales`.
     */
    'translatable' => [
        'with_fallback' => true,
        'fallback_locale' => 'en',
        'locales' => ['en', 'ar'],

        // Optional overrides used by the translation relationship trait.
        'translation_suffix' => 'Translation',
        'translation_model_namespace' => null,
    ],

    'auth' => [
        'guards' => [
            'admin' => env('NINJAPORTAL_ADMIN_GUARD', 'admin'),
        ],
    ],

    'user' => [
        'statuses' => [
            'active',
            'inactive',
            'pending',
        ],
        'default_status' => env('NINJAPORTAL_USER_DEFAULT_STATUS', 'pending'),
    ],

    'api_products' => [
        'default_visibility' => env('NINJAPORTAL_API_PRODUCT_DEFAULT_VISIBILITY', 'public'),
        'storage_disk' => env('NINJAPORTAL_API_PRODUCT_STORAGE_DISK', 'public'),
    ],

    'models' => [
        'Admin' => \NinjaPortal\Portal\Models\Admin::class,
        'ApiProduct' => \NinjaPortal\Portal\Models\ApiProduct::class,
        'Audience' => \NinjaPortal\Portal\Models\Audience::class,
        'Category' => \NinjaPortal\Portal\Models\Category::class,
        'Menu' => \NinjaPortal\Portal\Models\Menu::class,
        'MenuItem' => \NinjaPortal\Portal\Models\MenuItem::class,
        'Permission' => \Spatie\Permission\Models\Permission::class,
        'Role' => \Spatie\Permission\Models\Role::class,
        'Setting' => \NinjaPortal\Portal\Models\Setting::class,
        'SettingGroup' => \NinjaPortal\Portal\Models\SettingGroup::class,
        'User' => \NinjaPortal\Portal\Models\User::class,
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

    /**
     * Locales ex: ['en' => "English", 'ar' => "Arabic"]
     */
    'locales' => [
        'en' => 'English',
        'ar' => 'Arabic',
    ],

];
