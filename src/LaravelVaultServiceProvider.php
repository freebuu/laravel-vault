<?php

namespace YaSdelyal\LaravelVault;

use GuzzleHttp\Client;
use Http\Factory\Guzzle\RequestFactory;
use Http\Factory\Guzzle\StreamFactory;
use Illuminate\Support\ServiceProvider;
use YaSdelyal\LaravelVault\Commands\GetSecrets;
use YaSdelyal\LaravelVault\Commands\MoveEnv;
use YaSdelyal\LaravelVault\Drivers\ClientFactory;
use YaSdelyal\LaravelVault\Drivers\HashiCorpVault;

class LaravelVaultServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/vault.php' => config_path('vault.php'),
            ], 'config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/vault.php', 'vault');

        $this->app->singleton(LaravelVault::class, function ($app){
            return new LaravelVault($app);
        });

        $this->app->singleton(EnvFileService::class, function ($app){
            return new EnvFileService($app);
        });

        $this->app->alias(LaravelVault::class, 'vault');

        $this->registerCommands();
        $this->registerDriver();
    }

    private function registerCommands()
    {
        $this->commands([
            GetSecrets::class,
            MoveEnv::class,
        ]);
    }

    private function registerDriver()
    {
        $this->app->bind(ClientFactory::class, function (){
            return new ClientFactory(
                new Client(),
                new RequestFactory(),
                new StreamFactory()
            );
        });

        $this->app['vault']->extend('hashicorp_vault_kv_v1', function ($app, $name, $config){
            $client = $app->make(ClientFactory::class)->create($config['host'], $config['port'], $config);
            return new HashiCorpVault($client);
        });
    }
}