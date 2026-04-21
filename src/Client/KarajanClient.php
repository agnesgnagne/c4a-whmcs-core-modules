<?php

namespace WHMCS\Cloud4Africa\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Illuminate\Database\Capsule\Manager as Capsule;
use WHMCS\Cloud4Africa\Util\Translator;

class KarajanClient extends AbstractKarajanClient
{
    public function fetchAuthToken(string $serverType = 'karajan', bool $enableCache = false): array
    {
        if ($enableCache) {
            self::initializeTokenDatabaseSchema();
        }
        
        $token = $this->whmcsRepository->findValidKarajanToken();
        
        $accessToken = $token['access_token'] ?? null;
        $projectId = $token['project_id'] ?? null;
        
        if (! $accessToken) {
            $httpClient = $this->createClient($serverType);
            
            $server = $this->whmcsRepository->findKarajanServer($serverType);
            $results = $this->whmcsRepository->getDecryptedPassword($server['password']);
            $password = $results['password'];
            
            try {
                $response = $httpClient->request('POST', '/identity/v1/rest/auth/tokens', [
                    RequestOptions::HEADERS => [
                        'X-Application-Id' => $server['accesshash']
                    ],
                    RequestOptions::JSON => [
                        'identity' => [
                            'methods' => ['password'],
                            'password' => [
                                'user' => [
                                    'username' => $server['username'],
                                    'password' => $password
                                ]
                            ]
                        ]
                    ]
                ]);
            } catch (RequestException $e) {
                $statusCode = $e->getResponse()->getStatusCode();
                
                switch ($statusCode){
                    case 400:
                        $errorMessage = 'Bad Request';
                        break;
                    case 404:
                        $errorMessage = 'Not Found';
                        break;
                    default:
                        $errorMessage = 'Server Error';
                        break;
                }
                
                throw new \Exception($errorMessage);
            } catch (\Exception $e) {
                logModuleCall('c4a_whmcs', __FUNCTION__, [], $e->getMessage());
                throw new \Exception('Server Error');
            }
            
            $token = json_decode($response->getBody()->getContents(), true);
            
            if ($enableCache) {
                Capsule::table('c4a_karajan_token')->insert([
                    'access_token' => $token['tokens']['accessToken']['id'],
                    'project_id' => $token['user']['project']['id'],
                    'expires_at' => $token['tokens']['accessToken']['expiresAt']
                ]);
            }
            
            $accessToken = $token['tokens']['accessToken']['id'];
            $projectId = $token['user']['project']['id'];
        }
        
        return [
            'accessToken' => $accessToken,
            'projectId' => $projectId
        ];
    }
    
    private static function initializeTokenDatabaseSchema()
    {
        try {
            if (!Capsule::schema()->hasTable('c4a_karajan_token')) {
                Capsule::schema()->create(
                    'c4a_karajan_token',
                    function ($table) {
                        /** @var \Illuminate\Database\Schema\Blueprint $table */
                        $table->increments('id');
                        $table->text('access_token');
                        $table->text('project_id');
                        $table->datetime('expires_at');
                    }
                    );
            }
        } catch (\Exception $e) {
            logModuleCall('c4a_whmcs', __FUNCTION__, [], $e->getMessage());
            throw new \Exception($translator->trans('error.default'));
        }
        
        return;
    }
}
