<?php

return [

    /**
     * Storage Adapters
     * The storage adapter
     */
    'storage' => Benrowe\Laravel\Config\Adapters\Db::class,

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
