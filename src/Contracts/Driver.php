<?php

namespace TempNamespace\LaravelVault\Contracts;

interface Driver
{
    public function patch(string $patch): ?Variables;
    public function patches(array $patches): ?Variables;
}