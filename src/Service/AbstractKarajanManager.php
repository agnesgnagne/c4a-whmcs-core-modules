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
    
    
    /**
     * @return array<string, mixed>
     */
    public function fetchAuthToken(): array
    {
        return $this->karajanClient->fetchAuthToken();
    }
    
    /**
     * @param string $accessToken
     * @param array<string, mixed> $queryParams
     * @return Response
     */
    public function listAccounts(string $accessToken, array $queryParams = []): Response
    {
        return $this->karajanClient->request(
            'GET',
            sprintf('/orchestrator/v1/rest/accounts'),
            [
                RequestOptions::HEADERS => [
                    'Content-Type' => 'application/json',
                    'Authorization' => sprintf('Bearer %s', $accessToken),
                ],
                RequestOptions::QUERY => $queryParams,
            ]
            );
    }
    
    /**
     *
     * @param string $accessToken
     * @param string $accountId
     * @return Response
     */
    public function getAccount(string $accessToken, string $accountId): Response
    {
        return $this->karajanClient->request(
            'GET',
            sprintf('/orchestrator/v1/rest/accounts/%s', $accountId),
            [
                RequestOptions::HEADERS => [
                    'Content-Type' => 'application/json',
                    'Authorization' => sprintf('Bearer %s', $accessToken),
                ]
            ]
            );
    }
    
    /**
     *
     * @param string $accessToken
     * @param string $email
     * @return Response
     */
    public function getAccountByEmail(string $accessToken, string $email): Response
    {
        return $this->karajanClient->request(
            'GET',
            sprintf('/orchestrator/v1/rest/accounts'),
            [
                RequestOptions::HEADERS => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken
                ],
                RequestOptions::QUERY => [
                    'email' => $email,
                ],
            ]
            );
    }
    
    /**
     *
     * @param string $accessToken
     * @param array<string, mixed> $data
     * @return Response
     */
    public function createAccount(string $accessToken, array $data): Response
    {
        return $this->karajanClient->request(
            'POST',
            '/orchestrator/v1/rest/accounts',
            [
                RequestOptions::HEADERS => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken
                ],
                RequestOptions::JSON => $data
            ]
            );
    }
    
    /**
     *
     * @param string $accessToken
     * @param string $accountId
     * @param array<string, mixed> $data
     * @return Response
     */
    public function updateAccount(string $accessToken, string $accountId, array $data): Response
    {
        return $this->karajanClient->request(
            'PUT',
            sprintf('/orchestrator/v1/rest/accounts/%s', $accountId),
            [
                RequestOptions::HEADERS => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken
                ],
                RequestOptions::JSON => $data
            ]
            );
    }
    
    /**
     *
     * @param string $accessToken
     * @param string $accountId
     * @return Response
     */
    public function getAccountServices(string $accessToken, string $accountId): Response
    {
        return $this->karajanClient->request(
            'GET',
            sprintf('/orchestrator/v1/rest/accounts/%s/services', $accountId),
            [
                RequestOptions::HEADERS => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken
                ]
            ]
            );
    }
    
    /**
     *
     * @param string $accessToken
     * @param string $accountId
     * @param array<int, mixed> $services
     * @return Response
     */
    public function attachAccountServices(string $accessToken, string $accountId, array $services): Response
    {
        return $this->karajanClient->request(
            'PUT',
            sprintf('/orchestrator/v1/rest/accounts/%s/services', $accountId),
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
                RequestOptions::JSON => [
                    'services' => $services
                ]
            ]
            );
    }
    
    /**
     *
     * @param string $accessToken
     * @param string $accountId
     * @param array<int, mixed> $services
     * @return Response
     */
    public function detachAccountServices(string $accessToken, string $accountId, array $services): Response
    {
        return $this->karajanClient->request(
            'DELETE',
            sprintf('/orchestrator/v1/rest/accounts/%s/services', $accountId),
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
                RequestOptions::JSON => [
                    'serviceIds' => $services
                ]
            ]
            );
    }
    
    /**
     *
     * @param string $accessToken
     * @param string $accountId
     * @param array<int, mixed> $services
     * @return Response
     */
    public function promoteAccountServices(string $accessToken, string $accountId, array $services): Response
    {
        return $this->karajanClient->request(
            'PUT',
            sprintf('/orchestrator/v1/rest/accounts/%s/promote', $accountId),
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
                RequestOptions::JSON => [
                    'serviceIds' => $services
                ]
            ]
            );
    }
    
    /**
     *
     * @param string $accessToken
     * @param string $accountId
     * @param array<int, mixed> $services
     * @return Response
     */
    public function demoteAccountServices(string $accessToken, string $accountId, array $services): Response
    {
        return $this->karajanClient->request(
            'PUT',
            sprintf('/orchestrator/v1/rest/accounts/%s/demote', $accountId),
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
                RequestOptions::JSON => [
                    'serviceIds' => $services
                ]
            ]
            );
    }
    
    /**
     *
     * @param string $accessToken
     * @param string $accountId
     * @param array<int, mixed> $services
     * @return Response
     */
    public function lockAccountServices(string $accessToken, string $accountId, array $services): Response
    {
        return $this->karajanClient->request(
            'PUT',
            sprintf('/orchestrator/v1/rest/accounts/%s/lock', $accountId),
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
                RequestOptions::JSON => [
                    'serviceIds' => $services
                ]
            ]
            );
    }
    
    /**
     *
     * @param string $accessToken
     * @param string $accountId
     * @param array<int, mixed> $services
     * @return Response
     */
    public function unlockAccountServices(string $accessToken, string $accountId, array $services): Response
    {
        return $this->karajanClient->request(
            'PUT',
            sprintf('/orchestrator/v1/rest/accounts/%s/unlock', $accountId),
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
                RequestOptions::JSON => [
                    'serviceIds' => $services
                ]
            ]
            );
    }
    
    /**
     *
     * @param string $accessToken
     * @param string $accountId
     * @return Response
     */
    public function deleteAccount(string $accessToken, string $accountId): Response
    {
        return $this->karajanClient->request(
            'DELETE',
            sprintf('/orchestrator/v1/rest/accounts/%s', $accountId),
            [
                RequestOptions::HEADERS => [
                    'Authorization' => sprintf('Bearer %s', $accessToken)
                ]
            ]
            );
    }
    
    /**
     *
     * @param string $accessToken
     * @param string $userId
     * @param array<string, mixed> $data
     * @return Response
     */
    public function changeUserPassword(string $accessToken, string $userId, array $data): Response
    {
        return $this->karajanClient->request(
            'PUT',
            sprintf('/identity/v1/rest/users/%s/change/password', $userId),
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
                RequestOptions::JSON => $data
            ]
            );
    }
    
    /**
     *
     * @param string $accessToken
     * @param array<string, mixed> $data
     * @return Response
     */
    public function resetUserPassword(string $accessToken, array $data): Response
    {
        return $this->karajanClient->request(
            'POST',
            '/identity/v1/rest/users/password/resetting/request',
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
                RequestOptions::JSON => [
                    'data' => $data
                ]
            ]
            );
    }
    
    /**
     *
     * @param string $otp
     * @param array<string, mixed> $data
     * @return Response
     */
    public function confirmUserResettingPassword(string $otp, array $data): Response
    {
        return $this->karajanClient->request(
            'POST',
            '/identity/v1/rest/users/password/resetting/request',
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'OTP ' . $otp,
                    'Content-Type' => 'application/json'
                ],
                RequestOptions::JSON => $data
            ]
            );
    }
    
    /**
     *
     * @param string $accessToken
     * @return Response
     */
    public function listProvisioningAlgorithms(string $accessToken): Response
    {
        return $this->karajanClient->request(
            'GET',
            '/orchestrator/v1/rest/provisioning-algorithms',
            [
                RequestOptions::HEADERS => [
                    'Authorization' => sprintf('Bearer %s', $accessToken)
                ]
            ]
            );
    }
    
    /**
     *
     * @param string $accessToken
     * @param string $serviceId
     * @return Response
     */
    public function getService(string $accessToken, string $serviceId): Response
    {
        return $this->karajanClient->request(
            'GET',
            sprintf('/orchestrator/v1/rest/services/%s', $serviceId),
            [
                RequestOptions::HEADERS => [
                    'Authorization' => sprintf('Bearer %s', $accessToken),
                    'Accept' => 'application/json'
                ]
            ]
        );
    }
}

