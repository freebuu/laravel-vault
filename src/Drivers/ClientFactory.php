<?php


namespace TempNamespace\LaravelVault\Drivers;


use GuzzleHttp\Psr7\Uri;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Vault\AuthenticationStrategies\AppRoleAuthenticationStrategy;
use Vault\AuthenticationStrategies\AuthenticationStrategy;
use Vault\BaseClient;
use Vault\Client;

class ClientFactory
{
    /**
     * @var ClientInterface
     */
    private $client;
    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;
    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    public function __construct(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory
    )
    {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
    }


    public function create(string $host, string $port, array $config): BaseClient
    {
        $client = new Client(
            new Uri($host.':'.$port),
            $this->client,
            $this->requestFactory,
            $this->streamFactory
        );
        $client->setAuthenticationStrategy(
            $this->createAuthenticationStrategy($config)
        );
        return $client;
    }

    public function createAuthenticationStrategy(array $config): AuthenticationStrategy
    {
        //TODO выбор стратегии на основании предоставленных переменных
        return new AppRoleAuthenticationStrategy($config['role_id'], $config['secret_id'], $config['role_name'] ?? 'approle');
    }

}