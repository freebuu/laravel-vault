<?php

namespace YaSdelyal\LaravelVault\Commands;

class CiSecrets extends AbstractSecretsCommand
{

    protected $signature = 'vault:ci 
        {app : app patch variable} 
        {env : env patch variable} 
        {host : Vault host}
        {token : Vault token}
        {--connection : Set Vault connection, if not - default was used}
    ';

    protected $description = 'Shorthand command for using in CI - uses default connection';

    public function handle(): int
    {
        //собираем конфиг
        $connection = $this->argument('connection') ?? config('vault.default_connection');
        if (! $connection) {
            $this->error('No connection or default connection was specified');
            return 1;
        }
        $config = [
            'vars' => [
                'patch_variables' => [
                    'app' => $this->argument('app'),
                    'env' => $this->argument('env'),
                ],
            ],
            'connections' => [
                $connection => [
                    'host' => $this->argument('host'),
                    'token' => $this->argument('token'),
                ]
            ]
        ];
        if (! $this->mergeConfig($config)) {
            return 1;
        }

        if (! $this->makeOutput('currentEnv')) {
            return 1;
        }
        return 0;
    }
}
