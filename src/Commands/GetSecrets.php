<?php

namespace TempNamespace\LaravelVault\Commands;

use Illuminate\Console\Command;
use TempNamespace\LaravelVault\Contracts\Variables;
use TempNamespace\LaravelVault\LaravelVault;

class GetSecrets extends Command
{

    protected $signature = 'vault:get 
        {connection?}
        {--stdin} 
        {--b64}
        {--output=console}
    ';

    protected $description = 'Get env from Vault';

    public function handle(LaravelVault $vault): int
    {
        if ($this->option('stdin')) {
            $input = $this->secret('Pass config in JSON');
            $b64   = (bool) $this->option('b64');
            if(! $this->setConfigFromStdin($input, $b64)){
                return 1;
            };
        }
        $connection = $this->argument('connection');
        $variables = $vault->get($connection);
        $this->makeOutput($variables, $this->option('output'));
        return 0;
    }


    private function setConfigFromStdin(string $input, bool $b64 = false): bool
    {
        if($b64){
            $input = base64_decode($input, true);
            if(!is_string($input)){
                $this->error('Cannot parse base64 config from stdin');
                return false;
            }
        }
        $stdinConfig = json_decode($input, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            $this->error('Cannot parse JSON config from stdin: '. json_last_error_msg());
            return false;
        }
        $currentConfig = config('vault');
        $config = array_replace_recursive($currentConfig, $stdinConfig);
        if(! $config){
            $this->error('Cannot merge stdin config');
            return false;
        }
        config()->set('vault', $config);
        return true;
    }

    private function makeOutput(Variables $variables, string $type)
    {
        switch ($type):
            case 'console':
                $formatted = [];
                foreach ($variables->toArray() as $key => $value){
                    $formatted[] = [
                        'key' => $key,
                        'value' => $value
                    ];
                }
                $this->table(['Key', 'Value'], $formatted);
                break;
        endswitch;
    }
}