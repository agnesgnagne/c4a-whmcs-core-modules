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
    public function fetchIdentityAuthToken(): array;
    
    /**
     * @param string $accessToken
     * @param array<string, mixed> $queryParams
     * @return Response
     */
    public function listOrchestratorAccounts(string $accessToken, array $queryParams = []): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $accountId
     * @return Response
     */
    public function getOrchestratorAccount(string $accessToken, string $accountId): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $email
     * @return Response
     */
    public function getOrchestratorAccountByEmail(string $accessToken, string $email): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param array $data
     * @return Response
     */
    public function createOrchestratorAccount(string $accessToken, array $data): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $accountId
     * @param array $data
     * @return Response
     */
    public function updateOrchestratorAccount(string $accessToken, string $accountId, array $data): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $accountId
     * @return Response
     */
    public function getOrchestratorAccountServices(string $accessToken, string $accountId): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $accountId
     * @param array $services
     * @return Response
     */
    public function attachOrchestratorAccountServices(string $accessToken, string $accountId, array $services): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $accountId
     * @param array $services
     * @return Response
     */
    public function detachOrchestratorAccountServices(string $accessToken, string $accountId, array $services): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $accountId
     * @param array $services
     * @return Response
     */
    public function promoteOrchestratorAccountServices(string $accessToken, string $accountId, array $services): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $accountId
     * @param array $services
     * @return Response
     */
    public function demoteOrchestratorAccountServices(string $accessToken, string $accountId, array $services): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $accountId
     * @param array $services
     * @return Response
     */
    public function lockOrchestratorAccountServices(string $accessToken, string $accountId, array $services): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $accountId
     * @param array $services
     * @return Response
     */
    public function unlockOrchestratorAccountServices(string $accessToken, string $accountId, array $services): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $accountId
     * @return Response
     */
    public function deleteOrchestratorAccount(string $accessToken, string $accountId): Response;
    
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
    public function listOrchestratorProvisioningAlgorithms(string $accessToken): Response;
    
    /**
     * 
     * @param string $accessToken
     * @param string $serviceId
     * @return Response
     */
    public function getOrchestratorService(string $accessToken, string $serviceId): Response;
}

