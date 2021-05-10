<?php


namespace YaSdelyal\LaravelVault\Drivers\HashiCorpVaultV1;


use YaSdelyal\LaravelVault\Exceptions\DriveException;
use YaSdelyal\LaravelVault\Models\BasicVariables;
use YaSdelyal\LaravelVault\Contracts\Driver;
use YaSdelyal\LaravelVault\Contracts\Variables;
use Vault\Client;

class HashiCorpVault implements Driver
{
    private $isAuthenticated = false;
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function patch(string $patch): Variables
    {
        if(! $this->isAuthenticated){
            //TODO обработка ошибок
            if ($this->isAuthenticated = $this->client->authenticate()){
                throw new DriveException('Cannot authenticate');
            }
        }
        $response = $this->client->read($patch);
        return new BasicVariables($response->getData());
    }
}