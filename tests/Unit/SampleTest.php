<?php

namespace TempNamespace\LaravelVault\Tests\Unit;

use TempNamespace\LaravelVault\Drivers\HashiCorpVault;
use TempNamespace\LaravelVault\LaravelVault;
use TempNamespace\LaravelVault\Tests\TestCase;

class SampleTest extends TestCase
{
    public function testCanCreateVault()
    {
        $vault = $this->app->make('vault');
        $driver = $vault->connection('vault');
        $this->assertInstanceOf(LaravelVault::class, $vault);
        $this->assertInstanceOf(HashiCorpVault::class, $driver);
    }
}
