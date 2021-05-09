<?php


namespace TempNamespace\LaravelVault\Contracts;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

interface Variables extends Arrayable, Jsonable, JsonSerializable
{
    public function keys(): array;
    public function get($key): ?string;
    public function merge(Variables $variables): Variables;
    public function isEmpty(): bool;
}