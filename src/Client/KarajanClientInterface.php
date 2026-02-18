<?php

namespace WHMCS\Cloud4Africa\Client;

interface KarajanClientInterface
{
    public function createClient(string $serverType, bool $verify = false): Client;

    public function request(string $method = 'GET', string $url, array $options = [], string $serverType = 'karajan'): array;

    public function fetchAuthToken(): array;
}
