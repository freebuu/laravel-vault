<?php


namespace YaSdelyal\LaravelVault;

use Dotenv\Dotenv;
use Exception;
use YaSdelyal\LaravelVault\Contracts\Variables;
use YaSdelyal\LaravelVault\Exceptions\EnvFileException;
use Illuminate\Foundation\Application;

class EnvFileService
{
    public const NEXT = 'next';
    public const BAK  = 'backup';
    private $app;
    private $currentFile;
    private $nextFile;
    private $backupFile;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->currentFile = $this->getEnvFilePatch();
        $this->nextFile = $this->getEnvFilePatch(self::NEXT);
        $this->backupFile = $this->getEnvFilePatch(self::BAK);
    }

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
        $content = '';
        $vars = [];
        foreach ($variables->toArray() as $key => $value){
            //TODO maybe move formatter in dedicated?
            $key = strtoupper($key);
            $vars[] = $key;
            $content .= strtoupper($key).'='.$value."\n";
        }
        $this->checkOrFail($this->nextFile);

        if(! file_put_contents($this->nextFile, $content)){
            throw new EnvFileException('Cannot write to file ' . $this->nextFile);
        }
        try{
            $dotenv = Dotenv::create($this->app->environmentPath(), $this->getEnvFileName(self::NEXT));
            $dotenv->load();
            $dotenv->required($vars);
        }catch (Exception $exception){
            unlink($this->nextFile);
            throw new EnvFileException("Dotenv file {$this->nextFile} not write correctly");
        }
    }

    /**
     * @throws EnvFileException
     */
    public function backupCurrentEnv()
    {
        if(! $this->isFile($this->currentFile)){
            return;
        }
        $this->checkOrFail($this->backupFile);
        if(! copy($this->currentFile, $this->backupFile)){
            throw new EnvFileException('Cannot backup current .env to  ' . $this->backupFile);
        }
    }

    /**
     * @throws EnvFileException
     */
    public function moveNextEnvToCurrent()
    {
        if($this->isFile($this->nextFile)){
            throw new EnvFileException('Next file not created ' . $this->nextFile);
        }
        $this->backupCurrentEnv();
        if(! copy($this->nextFile, $this->currentFile)){
            throw new EnvFileException('Cannot move next .env to  ' . $this->currentFile);
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

    private function getEnvFilePatch(string $postfix = null): string
    {
        return $this->app->environmentPath() . DIRECTORY_SEPARATOR . $this->getEnvFileName($postfix);
    }

    private function getEnvFileName(string $postfix = null): string
    {
        return $this->app->environmentFile() . '.' . $postfix;
    }
}