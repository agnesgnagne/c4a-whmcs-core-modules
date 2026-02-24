<?php

namespace WHMCS\Cloud4Africa\Client;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;

interface KarajanClientInterface
{
    public function getBaseUrl(): string;
    
    public function setBaseUrl(string $baseUrl): self;
    
    public function createClient(string $serverType, bool $verify = false): Client;

    public function request(string $method, string $url, array $options = [], string $serverType = 'karajan'): ?Response;

    public function fetchAuthToken(): array;
}
