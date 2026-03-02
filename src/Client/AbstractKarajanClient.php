<?php

namespace WHMCS\Cloud4Africa\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

abstract class AbstractKarajanClient implements KarajanClientInterface
{
    private string $baseUrl;

    public function __construct()
    {
        $server = json_decode(Capsule::table('tblservers')->where('type', $serverType)->get(), true);

        $this->baseUrl = sprintf('%s://%s:%s', $server[0]['secure'] == 'on' ? 'https' : 'http', $server[0]['hostname'], $server[0]['port']);
    }

    public function createClient(string $serverType = 'karajan', bool $verify = false): Client
    {
        return new Client([
            'base_uri' => $this->baseUrl,
            'verify' => $verify
        ]);
    }

    public function setBaseUrl(string $baseUrl): AbstractKarajanClient
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function request(string $method, string $url, array $options = [], string $serverType = 'karajan'): ?Response
    {
        $httpClient = new Client(['verify' => $verify]);
        return $httpClient->request($method, $url, $options);
    }

    public function fetchAuthToken($serverType = 'karajan'): array
    {}
}
