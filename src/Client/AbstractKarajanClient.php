<?php

namespace WHMCS\Cloud4Africa\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use WHMCS\Cloud4Africa\Repository\WhmcsRepositoryInterface;

abstract class AbstractKarajanClient implements KarajanClientInterface
{
    /** @var WhmcsRepositoryInterface $whmcsRepository **/
    protected WhmcsRepositoryInterface $whmcsRepository;
    
    private string $baseUrl;
    
    public function __construct(WhmcsRepositoryInterface $whmcsRepository)
    {
        $this->whmcsRepository = $whmcsRepository;
    }
    
    public function createClient(string $serverType = 'karajan', bool $verify = false): Client
    {
        return new Client([
            'base_uri' => $this->getBaseUrl(),
            'verify' => $verify
        ]);
    }
    
    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }
    
    public function getBaseUrl(): string
    {
        $server = $this->whmcsRepository->findKarajanServer();
        return sprintf('%s://%s:%s', $server['secure'] == 'on' ? 'https' : 'http', $server['hostname'], $server['port']);
    }
    
    public function request(string $method, string $url, array $options = [], string $serverType = 'karajan'): ?Response
    {
        $httpClient = new Client([
            'verify' => empty($options['verify']) ? $options['verify'] : false,
            'base_uri' => $this->getBaseUrl(),
        ]);
        
        return $httpClient->request($method, $url, $options);
    }
    
    public function fetchAuthToken(string $serverType = 'karajan'): array
    {}
}
