<?php

namespace WHMCS\Cloud4Africa\Client;

use GuzzleHttp\Psr7\Response;

interface KarajanClientInterface
{
    public function createClient(string $serverType, bool $verify = false): Client;

    public function request(string $method = 'GET', string $url, array $options = [], string $serverType = 'karajan'): ?Response;

    public function fetchAuthToken(): array;
}
