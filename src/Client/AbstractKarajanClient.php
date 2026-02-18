<?php

namespace WHMCS\Cloud4Africa\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

abstract AbstractKarajanClient
{
    public function createClient(string $serverType = 'karajan', bool $verify = false): Client
    {
        $server = json_decode(Capsule::table('tblservers')->where('type', $serverType)->get(), true);

        if (true === empty($server[0])) {
            logModuleCall('c4a_whmcs', __FUNCTION__, [], $translator->trans('error.server_not_found'));
            throw new \Exception($translator->trans('error.default'));
        }

        return new Client([
            'base_uri' => sprintf('%s://%s:%s', $server[0]['secure'] == 'on' ? 'https' : 'http', $server[0]['hostname'], $server[0]['port']),
            'verify' => $verify
        ]);
    }

    public function request(string $method, string $url, array $options = [], string $serverType = 'karajan'): ?Response
    {
        $httpClient = $this->createClient($serverType);
        return $httpClient->request($method, $url, $options);
    }

    public function fetchAuthToken($serverType = 'karajan'): array
    {}
}
