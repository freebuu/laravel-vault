<?php


namespace TempNamespace\LaravelVault\Drivers;


use TempNamespace\LaravelVault\Models\BasicVariables;
use TempNamespace\LaravelVault\Contracts\Driver;
use TempNamespace\LaravelVault\Contracts\Variables;
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

    private function checkAuthenticated(): bool
    {
        if(! $this->isAuthenticated){
            //TODO обработка ошибок
            $this->isAuthenticated = $this->client->authenticate();
        }
        return $this->isAuthenticated;
    }

    public function patch(string $patch): Variables
    {
        //TODO обработка ошибок
        $this->checkAuthenticated();
        $response = $this->client->read($patch);
        return new BasicVariables($response->getData());
    }

    public function patches(array $patches): Variables
    {
        $variables = new BasicVariables([]);
        foreach ($patches as $patch){
            $variables->merge($this->patch($patch));
        }
        return $variables;
    }
}