<?php

namespace TempNamespace\LaravelVault\Commands;

use Dotenv\Dotenv;
use Exception;
use Illuminate\Console\Command;
use TempNamespace\LaravelVault\Contracts\Variables;
use TempNamespace\LaravelVault\LaravelVault;

class GetSecrets extends Command
{

    protected $signature = 'vault:get 
        {connection? : Set Vault connection from config}
        {--stdin : When present, command will be wait JSON config from stdin} 
        {--b64 : Work only with --stdin - when present, config must be base64 encoded}
        {--output=console : Where the vars will ne output. Possible: console, nextEnv}
    ';

    protected $description = 'Get env from Vault';

    public function handle(LaravelVault $vault): int
    {
        if ($this->option('stdin')) {
            $input = $this->secret('Pass config in JSON');
            $b64   = (bool) $this->option('b64');
            if(! $this->setConfigFromStdin($input, $b64)){
                return 1;
            }
        }
        $connection = $this->argument('connection');
        //TODO отображение получения конфигов по путям и контроль ошибок
        $vars = $vault->get($connection);
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
            $this->error('Unsupported format ' . $format);
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

    //TODO move to dedicated service
    private function nextEnvFormat(Variables $variables): bool
    {
        $name = $this->getLaravel()->environmentFile().'.next';
        $patch = $this->getLaravel()->environmentPath();
        $nextFile = $patch.DIRECTORY_SEPARATOR.$name;
        $content = '';
        $vars = [];
        foreach ($variables->toArray() as $key => $value){
            $key = strtoupper($key);
            $vars[] = $key;
            $content .= strtoupper($key).'='.$value."\n";
        }
        if(! is_file($nextFile) and ! touch($nextFile)){
            $this->error('Cannot create ' . $nextFile);
            return false;
        }
        if(! is_writable($nextFile)){
            $this->error('File is not writeable ' . $nextFile);
            return false;
        }
        if(! file_put_contents($nextFile, $content)){
            $this->error('Cannot write to file ' . $nextFile);
            return false;
        }
        try{
            $dotenv = Dotenv::create($patch, $name);
            $dotenv->load();
            $dotenv->required($vars);
        }catch (Exception $exception){
            $this->error("Dotenv file {$nextFile} not write correctly");
            unlink($nextFile);
            return false;
        }
        return true;
    }
}