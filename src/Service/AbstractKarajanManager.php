<?php

namespace WHMCS\Cloud4Africa\Service;

use WHMCS\Cloud4Africa\DTO\Template;
use WHMCS\Cloud4Africa\Repository\WhmcsRepositoryInterface;
use WHMCS\Cloud4Africa\Client\KarajanClientInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\RequestOptions;

abstract class AbstractKarajanManager implements KarajanManagerInterface
{
    /** @var KarajanClientInterface $karajanClient **/
    protected KarajanClientInterface $karajanClient;
    
    public function __construct(KarajanClientInterface $karajanClient)
    {
        $this->karajanClient = $karajanClient;
    }

    protected function getKarajanClient(): KarajanClientInterface
    {
        return $this->karajanClient;
    }

    public function getService(string $baseUrl, string $accessToken, string $serviceId): ResponseInterface
    {
        return $this->karajanClient->request(
            'GET',
            sprintf('%s/orchestrator/v1/rest/services/%s', $baseUrl, $serviceId),
            [
                RequestOptions::HEADERS => [
                    'Authorization' => sprintf('Bearer %s', $accessToken),
                    'Accept' => 'application/json'
                ]
            ]
        );
    }
}
