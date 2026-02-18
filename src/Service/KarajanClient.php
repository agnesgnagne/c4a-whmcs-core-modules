<?php

namespace WHMCS\Cloud4Africa\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Illuminate\Database\Capsule\Manager as Capsule;
use WHMCS\Cloud4Africa\Util\Translator;

class KarajanClient
{
    public static function createHttpClient()
    {
        $server = json_decode(Capsule::table('tblservers')->where('type', 'karajan')->get(), true);

        if (true === empty($server[0])) {
            logModuleCall('c4a_whmcs', __FUNCTION__, [], $translator->trans('error.server_not_found'));
            throw new \Exception($translator->trans('error.default'));
        }

        return new Client([
            'base_uri' => sprintf('%s://%s:%s', $server[0]['secure'] == 'on' ? 'https' : 'http', $server[0]['hostname'], $server[0]['port']),
            'verify' => false
        ]);
    }

    public static function fetchAuthToken()
    {
        self::initializeTokenDatabaseSchema();

        $translator = new Translator();
        $now = date('c');
        $token = Capsule::table('c4a_karajan_token')->where('expires_at', '>', $now)->first();
        $accessToken = $token->access_token;
        $projectId = $token->project_id;

        if (! $accessToken) {
            $httpClient = self::createHttpClient();

            $server = json_decode(Capsule::table('tblservers')->where('type', 'karajan')->get(), true);
            $results = localAPI('DecryptPassword', ['password2' => $server[0]['password']]);
            $password = $results['password'];

            try {
                $response = $httpClient->request('POST', '/identity/v1/rest/auth/tokens', [
                    RequestOptions::HEADERS => [
                        'X-Application-Id' => $server[0]['accesshash']
                    ],
                    RequestOptions::JSON => [
                        'identity' => [
                            'methods' => ['password'],
                            'password' => [
                                'user' => [
                                    'username' => $server[0]['username'],
                                    'password' => $password
                                ]
                            ]
                        ]
                    ]
                ]);
            } catch (RequestException $e) {
                $statusCode = $e->getResponse()->getStatusCode();
                $errorMessage = ($statusCode === 400) ? $translator->trans('error.bad_request') : (($statusCode === 404) ?
                $translator->trans('error.not_found') : $translator->trans('error.default'));

                logModuleCall('c4a_whmcs', __FUNCTION__, [], $e->getResponse()->getBody()->getContents());
                throw new \Exception($errorMessage);
            } catch (\Exception $e) {
                logModuleCall('c4a_whmcs', __FUNCTION__, [], $e->getMessage());
                throw new \Exception($translator->trans('error.default'));
            }

            $token = json_decode($response->getBody()->getContents(), true);

            Capsule::table('c4a_karajan_token')->insert([
                'access_token' => $token['tokens']['accessToken']['id'],
                'project_id' => $token['user']['project']['id'],
                'expires_at' => $token['tokens']['accessToken']['expiresAt']
            ]);

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
