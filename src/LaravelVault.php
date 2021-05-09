<?php

namespace TempNamespace\LaravelVault;

use Closure;
use Illuminate\Foundation\Application;
use InvalidArgumentException;
use TempNamespace\LaravelVault\Contracts\Driver;

class LaravelVault
{
    private $driverCreators = [];
    private $connections = [];
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function extend(string $name, Closure $closure)
    {
        $this->driverCreators[$name] = $closure;
    }

    public function connection(string $name = null): Driver
    {
        if(! $name = $name ?? $this->getDefaultConnectionName()){
            throw new InvalidArgumentException('No connection or default_connection was specified');
        }
        $config = $this->getConnectionConfig($name);
        return $this->connections[$name] ?? $this->connections[$name] = $this->resolveDriver($config['driver'], $config);
    }


    private function resolveDriver(string $name, array $config): Driver
    {
        if(! isset($this->driverCreators[$name])){
            throw new InvalidArgumentException("Vault driver [{$name}] is not defined.");
        }
        return $this->driverCreators[$name]($this->app, $name, $config);
    }

    private function getDefaultConnectionName(): string
    {
        return $this->app['config']['vault.default_connection'];
    }

    private function getConnectionConfig(string $name): array
    {
        if(! $config = $this->app['config']['vault.connections.' . $name] or ! is_array($config)){
            throw new InvalidArgumentException("Vault connection [{$name}] is not defined.");
        }
        return $config;
    }

}