<?php


namespace TempNamespace\LaravelVault\Drivers;


use TempNamespace\LaravelVault\Contracts\Driver;
use Vault\Client;

class HashiCorpVault implements Driver
{

    private $isAuthenticated = false;

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    private function isAuthenticated(): bool
    {
        if(! $this->isAuthenticated){
            $this->isAuthenticated = $this->client->authenticate();
        }
        return $this->isAuthenticated;
    }
}