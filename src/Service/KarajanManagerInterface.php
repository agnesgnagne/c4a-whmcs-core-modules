<?php

namespace WHMCS\Cloud4Africa\Service;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use WHMCS\Cloud4Africa\Client\KarajanClientInterface;

interface KarajanManagerInterface
{
    /**
     * @return array
     */
    public function fetchAuthToken(): array;
    
    /**
     * @param string $accessToken
     * @param array<string, mixed> $queryParams
     * @return Response
     */
    public function listAccounts(string $accessToken, array $queryParams = []): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $accountId
     * @return Response
     */
    public function getAccount(string $accessToken, string $accountId): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $email
     * @return Response
     */
    public function getAccountByEmail(string $accessToken, string $email): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param array $data
     * @return Response
     */
    public function createAccount(string $accessToken, array $data): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $accountId
     * @param array $data
     * @return Response
     */
    public function updateAccount(string $accessToken, string $accountId, array $data): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $accountId
     * @return Response
     */
    public function getAccountServices(string $accessToken, string $accountId): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $accountId
     * @param array $services
     * @return Response
     */
    public function attachAccountServices(string $accessToken, string $accountId, array $services): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $accountId
     * @param array $services
     * @return Response
     */
    public function detachAccountServices(string $accessToken, string $accountId, array $services): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $accountId
     * @param array $services
     * @return Response
     */
    public function promoteAccountServices(string $accessToken, string $accountId, array $services): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $accountId
     * @param array $services
     * @return Response
     */
    public function demoteAccountServices(string $accessToken, string $accountId, array $services): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $accountId
     * @param array $services
     * @return Response
     */
    public function lockAccountServices(string $accessToken, string $accountId, array $services): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $accountId
     * @param array $services
     * @return Response
     */
    public function unlockAccountServices(string $accessToken, string $accountId, array $services): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $accountId
     * @return Response
     */
    public function deleteAccount(string $accessToken, string $accountId): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $userId
     * @param array $data
     * @return Response
     */
    public function changeUserPassword(string $accessToken, string $userId, array $data): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param array $data
     * @return Response
     */
    public function resetUserPassword(string $accessToken, array $data): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param array $data
     * @return Response
     */
    public function confirmUserResettingPassword(string $accessToken, array $data): Response;
    
    /**
     * 
     * @param string $accessToken
     * @return Response
     */
    public function listProvisioningAlgorithms(string $accessToken): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $serviceId
     * @return Response
     */
    public function getService(string $accessToken, string $serviceId): Response;
}

