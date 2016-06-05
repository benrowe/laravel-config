<?php

return [

    /**
     * Storage Adapters
     * The storage adapter
     */
    'storage' => [
        'enabled' => true,
        'driver' => 'file', // redis, file, pdo, custom
        'path' => storage_path('config'), // For file driver
        'connection' => null, // leave null for default connection
        'provider' => '', // instance of StorageInterface for custom driver
        // Benrowe\Laravel\Config\Adapters\Db::class,
    ],

    /**
     * Anytime a value is created/updated/deleted, do you want to automatically
     * persist this change through your storage adapater
     */
    'autoStore' => false,

    /**
     * The list of modifiers you want to allow into your Config instance
     */
    'modifiers' => [
        Benrowe\Laravel\Config\Modifiers\Json::class,
    ]
];
