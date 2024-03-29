<?php

namespace FreeBuu\LaravelVault\Facades;

use Closure;
use Illuminate\Support\Facades\Facade;
use FreeBuu\LaravelVault\Contracts\Driver;
use FreeBuu\LaravelVault\Contracts\Variables;

/**
 * @method static Driver connection(string $name = null)
 * @method static void extend(string $name, Closure $closure)
 * @method static Variables get(string $connection = null)
 * @method static Variables patch(string $patch)
 * @method static Variables patches(array $patches)
 */
class Vault extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'vault';
    }
}
