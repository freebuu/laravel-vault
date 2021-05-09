<?php


namespace TempNamespace\LaravelVault\Commands;


use Illuminate\Console\Command;
use TempNamespace\LaravelVault\EnvFileService;
use TempNamespace\LaravelVault\Exceptions\EnvFileException;

class MoveEnv extends Command
{
    protected $signature = 'vault:move-env';

    protected $description = 'Move .env.next to .env';

    public function handle(EnvFileService $envFileService): int
    {
        try {
            $envFileService->moveNextEnvToCurrent();
            return 0;
        } catch (EnvFileException $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}