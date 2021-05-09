<?php

namespace TempNamespace\LaravelVault\Tests;

use TempNamespace\LaravelVault\LaravelVaultServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelVaultServiceProvider::class
        ];
    }
}
