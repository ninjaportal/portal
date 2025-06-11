<?php

return [
    /*
     * the type of the platform
     * either "edge" or "apigeex" are supported
     *
     * this is used to determine the type of the platform
     */
    "apigee_platform" => "edge",

    'translatable' => [
        'translation_suffix' => 'Translation',
        'translation_model_namespace' => null,
    ],

    'settings' => [
        'cache' => [
            'ttl' => 60 * 60,
            'enabled' => true,
            'key' => 'portal.settings',
        ],
    ],

    /**
     * Locales ex: ['en' => "English", 'ar' => "Arabic"]
     */
    "locales" => [
        'en' => "English",
        'ar' => "Arabic",
    ]
];
