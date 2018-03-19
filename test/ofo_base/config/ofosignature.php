<?php

return [

    /**
     * server authorized apps.
     * note the `name` alpha_num_dash character allowed only.
     */
    'apps' => [
        [
            'name' => 'test',
            'secret' => env('APP_SECRET_test')
        ]
    ],

    /**
     * Skip the signature checking if in a debug environment.
     */
    'skip_if_debug' => true
];