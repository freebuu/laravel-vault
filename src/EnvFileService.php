<?php


namespace YaSdelyal\LaravelVault;

use Dotenv\Dotenv;
use Exception;
use YaSdelyal\LaravelVault\Contracts\Variables;
use YaSdelyal\LaravelVault\Exceptions\EnvFileException;

class EnvFileService
{

    /**
     * @throws EnvFileException
     */
    public function saveCurrentEnv(Variables $variables): void
    {
        $this->saveNextEnv($variables);
        $this->moveNextEnvToCurrent();
    }

    /**
     * @throws EnvFileException
     */
    public function saveNextEnv(Variables $variables): void
    {

        $name = app()->environmentFile().'.next';
        $patch = app()->environmentPath();
        $nextFile = $patch.DIRECTORY_SEPARATOR.$name;
        $content = '';
        $vars = [];
        foreach ($variables->toArray() as $key => $value){
            $key = strtoupper($key);
            $vars[] = $key;
            $content .= strtoupper($key).'='.$value."\n";
        }
        $this->checkOrFail($nextFile);

        if(! file_put_contents($nextFile, $content)){
            throw new EnvFileException('Cannot write to file ' . $nextFile);
        }
        try{
            $dotenv = Dotenv::create($patch, $name);
            $dotenv->load();
            $dotenv->required($vars);
        }catch (Exception $exception){
            unlink($nextFile);
            throw new EnvFileException("Dotenv file {$nextFile} not write correctly");
        }
    }

    /**
     * @throws EnvFileException
     */
    public function backupCurrentEnv()
    {
        $name = app()->environmentFile();
        $patch = app()->environmentPath();
        $backupName = $name.'.backup';
        $currentFile = $patch.DIRECTORY_SEPARATOR.$name;
        $backupFile = $patch.DIRECTORY_SEPARATOR.$backupName;
        if(! $this->isFile($currentFile)){
            return;
        }
        $this->checkOrFail($backupFile);
        if(! copy($currentFile, $backupFile)){
            throw new EnvFileException('Cannot backup current .env to  ' . $backupFile);
        }
    }

    /**
     * @throws EnvFileException
     */
    public function moveNextEnvToCurrent()
    {
        $name = app()->environmentFile();
        $patch = app()->environmentPath();
        $nextName = $name.'.next';
        $currentFile = $patch.DIRECTORY_SEPARATOR.$name;
        $nextFile = $patch.DIRECTORY_SEPARATOR.$nextName;
        if($this->isFile($nextFile)){
            throw new EnvFileException('Next file not created ' . $patch);
        }
        $this->backupCurrentEnv();
        if(! copy($nextFile, $currentFile)){
            throw new EnvFileException('Cannot move next .env to  ' . $nextFile);
        }
    }


    private function isFile(string $patch): bool
    {
        return is_file($patch) and is_readable($patch);
    }

    /**
     * @throws EnvFileException
     */
    private function checkOrFail(string $patch): void
    {
        if(! $this->isFile($patch) and ! touch($patch)){
            throw new EnvFileException('Cannot create ' . $patch);
        }
        if(! is_writable($patch)){
            throw new EnvFileException('File is not writeable ' . $patch);
        }
    }



}