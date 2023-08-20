<?php

namespace FreeBuu\LaravelVault\Tests;

use FreeBuu\LaravelVault\LaravelVaultServiceProvider;

/**
 * Class TestCase
 * @todo clean tmp folder after all tests
 * @package FreeBuu\LaravelVault\Tests
 */
class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        //TODO work with filesystem from adapter, not direct. Cons: Filesystem::class has not isWritable
        $this->app->useEnvironmentPath(implode(DIRECTORY_SEPARATOR, [__DIR__, 'tmp']));
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelVaultServiceProvider::class
        ];
    }
}
