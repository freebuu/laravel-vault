<?php

namespace TempNamespace\LaravelVault\Commands;

use Illuminate\Console\Command;

class GetSecrets extends Command
{

    protected $signature = 'vault:get';

    protected $description = 'Get env from Vault';

    public function handle()
    {
        $this->info('Getting env from vault');
    }

}