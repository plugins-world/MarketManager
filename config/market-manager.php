<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

return [
    'name' => 'MarketManager',

    'paths' => [
        'base' => base_path('extensions'),
        'markets' => base_path('storage/extensions'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Composer File Template
    |--------------------------------------------------------------------------
    |
    | YOU COULD CUSTOM HERE
    |
    */
    'composer'  => [
        'vendor' => 'plugins-world',
        'author' => [
            [
                'name'  => 'MouYong',
                'email' => 'my24251325@gmail.com',
                'homepage' => 'https://plugins-world.org/',
                'role' => 'Creator',
            ],
        ],
    ],
];
