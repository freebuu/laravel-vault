<?php

namespace YaSdelyal\LaravelVault\Tests;

use YaSdelyal\LaravelVault\LaravelVaultServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelVaultServiceProvider::class
        ];
    }
}
