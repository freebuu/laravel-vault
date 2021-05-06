<?php

namespace TempNamespace\LaravelVault;

use Illuminate\Support\ServiceProvider;

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
    }
}