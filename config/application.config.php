<?php

return [
    'modules' => [
        'Laminas\Router',
        'Laminas\Validator',
        'Laminas\Session',
        'Laminas\Db',
        'Application',
        'Web',
    ],
    'module_listener_options' => [
        'module_paths' => [
            './module',
            './vendor',
        ],
        'config_glob_paths' => [
            'config/autoload/{,*.}{global,local}.php',
        ],
        'config_cache_enabled' => false,
        'module_map_cache_enabled' => false,
    ],
];
