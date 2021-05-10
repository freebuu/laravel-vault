<?php


namespace YaSdelyal\LaravelVault\Tests\Unit;

use Illuminate\Foundation\Testing\WithFaker;
use YaSdelyal\LaravelVault\EnvFileService;
use YaSdelyal\LaravelVault\Models\BasicVariables;
use YaSdelyal\LaravelVault\Tests\TestCase;

class EnvFileServiceTest extends TestCase
{
    use WithFaker;

    protected $vars;
    protected $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->vars = new BasicVariables([
           'var1' => $this->faker->word,
           'var2' => $this->faker->word,
        ]);
        //TODO work with filesystem from adapter, not direct. Cons: Filesystem::class has not isWritable
        $this->app->useEnvironmentPath(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'tmp']));
        $this->service = new EnvFileService($this->app);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $stubs = [
            $this->service->getCurrentFile(),
            $this->service->getNextFile(),
            $this->service->getBackupFile(),
        ];
        foreach ($stubs as $stub){
            if(is_file($stub)){
                unlink($stub);
            }
        }
    }


    public function testSaveNextEnv()
    {
        $this->service->saveNextEnv($this->vars);
        $this->assertFileSameAsVars($this->service->getNextFile());
    }

    public function testSaveCurrent()
    {
        $this->service->saveCurrentEnv($this->vars);
        $this->assertFileSameAsVars($this->service->getCurrentFile());
    }

    private function assertFileExist(string $patch)
    {
        $this->assertTrue(is_file($patch));
    }

    private function assertFileSameAsVars($patch)
    {
        $this->assertFileExist($patch);
        $contents = file_get_contents($patch);
        $this->assertSame($this->vars->toEnv(), $contents);
    }

}