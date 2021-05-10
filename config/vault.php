<?php

return [
    'vars' => [
        /*
         * Patches for Driver
         * Envs from all patches will be merged
         * Can be used variables like {app} - for this, put it into patch_variables array
         */
        'patches' => [
            '/secret/{app}/{env}',
            '/secret/{app}/common',
        ],

        /*
         * Variables for patches
         * They will be substituted at render time
         */
        'patch_variables' => [
            'env' => config('app.env'),
            'app' => config('app.name')
        ],
    ],
    'default_connection' => env('VAULT_DEFAULT_CONNECTION', 'vault'),
    'connections' => [
        'vault' => [
            'driver'    => 'hashicorp_vault_v1',
            'host'      => ENV('VAULT_HOST', '127.0.0.1'),
            'port'      => ENV('VAULT_PORT', 8200),
            'role_id'   => ENV('VAULT_ROLE_ID', 'role_id'),
            'secret_id' => ENV('VAULT_SECRET_ID', 'secret_id'),
            'role_name' => ENV('VAULT_ROLE_NAME', 'approle'),
        ]
    ],
];