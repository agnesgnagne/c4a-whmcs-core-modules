<?php

namespace WHMCS\Cloud4Africa\Client;

interface KarajanClientInterface
{
    public function createClient(string $serverType, bool $verify = false): Client;

    public function fetchAuthToken(): array;
}
