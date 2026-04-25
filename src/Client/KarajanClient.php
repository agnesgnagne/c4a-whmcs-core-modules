<?php

namespace WHMCS\Cloud4Africa\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Illuminate\Database\Capsule\Manager as Capsule;
use WHMCS\Cloud4Africa\Util\Translator;
use WHMCS\Cloud4Africa\Traits\LoggerTrait;

class KarajanClient extends AbstractKarajanClient
{
    use LoggerTrait;
    
    public function fetchAuthToken(string $serverType): array
    {
        $server   = $this->whmcsRepository->findKarajanServer($serverType);
        $password = $this->whmcsRepository->getDecryptedPassword($server['password'])['password'];
        
        $cacheKey = $this->buildCacheKey('POST', '/identity/v1/rest/auth/tokens', [
            'server' => $serverType
        ]);
        
        try {
            $tokenData = $this->cacheManager->get(
                $cacheKey,
                function () use ($server, $password): array {
                    return $this->requestNewToken($server, $password);
                },
                ttl: 0
            );
            
        } catch (RequestException $e) {
            $statusCode   = $e->getResponse()?->getStatusCode();
            $errorMessage = match ($statusCode) {
                400 => 'Bad Request',
                401 => 'Unauthorized',
                404 => 'Not Found',
                default => 'Server Error',
            };
            throw new \Exception($errorMessage);
            
        } catch (\Exception $e) {
            $this->log([
                'moduleName' => 'redis',
                'action' => __FUNCTION__,
                'request' => ['serverType' => $serverType],
                'response' => $e->getMessage()
            ]);
            
            throw new \Exception('Server Error');
        }
        
        return $tokenData;
    }
    
    /**
     * @param array<tring, mixed> $server
     * @param string $password
     * @return array<tring, mixed>
     */
    private function requestNewToken(array $server, string $password): array
    {
        $httpClient = $this->createClient($server['type']);
        
        $response = $httpClient->request('POST', '/identity/v1/rest/auth/tokens', [
            RequestOptions::HEADERS => [
                'X-Application-Id' => $server['accesshash'],
                'Content-Type' => 'application/json',
            ],
            RequestOptions::JSON => [
                'identity' => [
                    'methods'  => ['password'],
                    'password' => [
                        'user' => [
                            'username' => $server['username'],
                            'password' => $password,
                        ],
                    ],
                ],
            ],
        ]);
        
        $body = json_decode($response->getBody()->getContents(), true);
        $token = $body['tokens']['accessToken'] ?? null;
        
        if (!$token || empty($token['id']) || empty($token['expiresAt'])) {
            throw new \Exception('Bad Response');
        }
        
        $expiresAt = new \DateTimeImmutable($token['expiresAt']);
        $now = new \DateTimeImmutable('now');
        $ttlSeconds = max(60, $expiresAt->getTimestamp() - $now->getTimestamp() - 60);
        
        return [
            'accessToken' => $token['id'],
            'projectId' => $body['user']['project']['id'] ?? '',
            'expiresAt' => $token['expiresAt'],
            '__ttl' => $ttlSeconds,
        ];
    }
}
