<?php

namespace YaSdelyal\LaravelVault\Commands;

use Illuminate\Console\Command;
use YaSdelyal\LaravelVault\Contracts\Variables;
use YaSdelyal\LaravelVault\EnvFileService;
use YaSdelyal\LaravelVault\Exceptions\EnvFileException;
use YaSdelyal\LaravelVault\LaravelVault;

class GetSecrets extends Command
{

    protected $signature = 'vault:get 
        {connection? : Set Vault connection from config}
        {--stdin : When present, command will be wait JSON config from stdin} 
        {--b64 : Work only with --stdin - when present, config must be base64 encoded}
        {--output=console : Where the vars will be output. Possible: console, nextEnv, currentEnv}
    ';

    protected $description = 'Get env from Vault';

    /** @var LaravelVault */
    protected $vault;
    /** @var EnvFileService */
    protected $envFileService;

    public function handle(LaravelVault $vault, EnvFileService $envFileService): int
    {
        $this->vault = $vault;
        $this->envFileService = $envFileService;
        if ($this->option('stdin')) {
            $input = $this->secret('Pass config in JSON');
            $b64   = (bool) $this->option('b64');
            if(! $this->setConfigFromStdin($input, $b64)){
                return 1;
            }
        }
        $connection = $this->argument('connection');
        //TODO отображение получения конфигов по путям и контроль ошибок
        $vars = $this->vault->get($connection);
        if($vars->isEmpty()){
            $this->error('Vars is empty, possible errors');
            return 1;
        }

        if(! $this->makeOutput($vars, $this->option('output'))){
            return 1;
        }
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

    private function makeOutput(Variables $variables, string $format): bool
    {
        $method = $format . 'Format';
        if(! method_exists($this, $method)){
            $this->error('Unsupported output format' . $format);
            return false;
        }
        return $this->{$method}($variables);
    }

    private function consoleFormat(Variables $variables): bool
    {
        $formatted = [];
        foreach ($variables->toArray() as $key => $value){
            $formatted[] = [
                'key' => $key,
                'value' => $value
            ];
        }
        $this->table(['Key', 'Value'], $formatted);
        return true;
    }

    private function nextEnvFormat(Variables $variables): bool
    {
        try {
            $this->envFileService->saveNextEnv($variables);
            $this->info('Env saved to next');
            return true;
        } catch (EnvFileException $e) {
            $this->error($e->getMessage());
            return false;
        }
    }

    private function currentEnvFormat(Variables $variables): bool
    {
        try {
            $this->envFileService->saveCurrentEnv($variables);
            $this->info('Env saved to current');
            return true;
        } catch (EnvFileException $e) {
            $this->error($e->getMessage());
            return false;
        }
    }
}