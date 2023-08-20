<?php

namespace FreeBuu\LaravelVault\Contracts;

interface Driver
{
    public function patch(string $patch): ?Variables;
}
