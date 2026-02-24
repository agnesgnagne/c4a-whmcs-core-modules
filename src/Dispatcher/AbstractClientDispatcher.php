<?php

namespace WHMCS\Cloud4Africa\Dispatcher;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use WHMCS\Cloud4Africa\Client\KarajanClient;
use WHMCS\Cloud4Africa\Client\KarajanClientInterface;
use WHMCS\Cloud4Africa\Repository\WhmcsLocalApiManager;
use WHMCS\Cloud4Africa\Repository\WhmcsRepositoryInterface;
use WHMCS\Cloud4Africa\Translation\TranslatorInterface;
use Illuminate\Database\Capsule\Manager as Capsule;
use WHMCS\Module\Addon\c4a_mailbox_carbonio\Client\Controller;
use WHMCS\Module\Addon\c4a_mailbox_carbonio\Client\Service\TemplateResolver;
use WHMCS\Module\Addon\c4a_mailbox_carbonio\Client\Translation\Translator;
use WHMCS\Module\Addon\c4a_mailbox_carbonio\Client\Service\TemplateManager;

abstract class AbstractClientDispatcher implements DispatcherInterface
{
    /** @var Translator $translator **/
    private $translator;
    
    /** @var WhmcsRepositoryInterface $whmcsRepository **/
    private WhmcsRepositoryInterface $whmcsRepository;
    
    /** @var KarajanClientInterface $karajanClient **/
    private KarajanClientInterface $karajanClient;
    
    /** @var TemplateManager $templateManager **/
    private TemplateManager $templateManager;

    /** @var ControllerInterface $controller **/
    private ControllerInterface $controller;
    
    /** @var array $parameters **/
    private $parameters;
    
    /**
     * @param Translator $translator
     * @param WhmcsRepositoryInterface $whmcsRepository
     * @param KarajanClientInterface $karajanClient
     * @param TemplateManager $templateManager
     * @param array $parameters
     */
    public function __construct(
        Translator $translator, 
        WhmcsRepositoryInterface $whmcsRepository, 
        KarajanClientInterface $karajanClient,
        TemplateManager $templateManager,
        ControllerInterface $controller,
        array $parameters
    )
    {
        $this->translator = $translator;
        $this->whmcsRepository = $whmcsRepository;
        $this->karajanClient = $karajanClient;
        $this->templateManager = $templateManager;
        $this->controller = $controller;
        $this->parameters = $parameters;
    }
    
    /**
     * Dispatch request.
     *
     * @param string $action
     * @param array $parameters
     *
     * @return array
     */
    public function dispatch(string $action, string $hostingId = null): ?Response
    {
        if ($hostingId) {
            $hosting = json_decode(Capsule::table('tblhosting')->where('id', $hostingId)->get(), true);

            if (true === empty($hosting[0])) {
                logModuleCall($this->parameters['moduleName'], __FUNCTION__, [], $this->translator->trans('client_dispatch.error.hosting_not_found'));
                throw new \Exception($this->translator->trans('client_dispatch.error.default'));
            }

            $this->parameters['hosting'] = $hosting[0];
            $product = json_decode(Capsule::table('tblproducts')->where('id', $hosting[0]['packageid'])->get(), true);

            if (true === empty($product[0])) {
                logModuleCall($this->parameters['moduleName'], __FUNCTION__, [], $this->translator->trans('client_dispatch.error.product_not_found'));
                throw new \Exception($this->translator->trans('client_dispatch.error.default'));
            }

            $this->parameters['product'] = $product[0]; // Pass product package to controller

            $serviceIdField = Capsule::table('tblcustomfields')->where('fieldname', 'serviceId')->where('relid', $hosting[0]['packageid'])->get();
            $serviceIdFieldValue = Capsule::table('tblcustomfieldsvalues')
                ->where('fieldid', $serviceIdField[0]->id)
                ->where('relid', $_GET['id'])
                ->get()
            ;
            
            if (true === empty($serviceIdFieldValue[0]->value)) {
                logModuleCall($this->parameters['moduleName'], __FUNCTION__, [], $this->translator->trans('client_dispatch.error.service_id_not_found'));
                throw new \Exception($this->translator->trans('client_dispatch.error.default'));
            }
            
            $token = $this->karajanClient->fetchAuthToken();
            
            $this->parameters['accessToken'] = $token['accessToken'];

            try {
                $response = $this->karajanClient->request(
                    'GET', 
                    sprintf('%s/orchestrator/v1/rest/services/%s', $this->karajanClient->getBaseUrl(), $serviceIdFieldValue[0]->value), 
                    [
                        RequestOptions::HEADERS => [
                            'Authorization' => sprintf('Bearer %s', $this->parameters['accessToken'])
                        ]
                    ]
                );
            } catch (RequestException $e) {
                logModuleCall($this->parameters['moduleName'], __FUNCTION__, [], $e->getResponse()->getBody()->getContents());
                throw new \Exception($this->translator->trans('mailbox_carbonio.error.default'));
            } catch (\Exception $e) {
                logModuleCall($this->parameters['moduleName'], __FUNCTION__, [], $e->getMessage());
                throw new \Exception($this->translator->trans('mailbox_carbonio.error.default'));
            }
            
            $service = json_decode($response->getBody()->getContents(), true);
            
            $this->parameters['region'] = $service['region']['displayName'] ?? $service['region']['name'];
            $this->parameters['resourceRecordSets'] = $service['resourceRecordSets'];

            foreach ($service['links'] as $link) {
                $this->parameters[$link['rel']] = $link['href'];
            }

            $product = json_decode(Capsule::table('tblproducts')->where('id', $hosting[0]['packageid'])->get(), true);

            if (true === empty($product[0])) {
                logModuleCall($this->parameters['moduleName'], __FUNCTION__, [], $this->translator->trans('client_dispatch.error.product_not_found'));
                throw new \Exception($this->translator->trans('client_dispatch.error.default'));
            }

            $this->parameters['product'] = $product[0];
        }
        
        $this->parameters = $this->buildExtraParameters($hostingId);

        $controller = new Controller($this->translator, $this->whmcsRepository, $this->karajanClient, $this->templateManager);
        
        if (is_callable([$controller, $action])) {
            $response = $controller->$action($this->parameters);

            if (!$response instanceof Response) {
                throw new \RuntimeException(
                    sprintf('Controller action %s must return Response', $action)
                );
            }

            return $response;

            if ($response instanceof Response) {
                return $response->send();
            }

            return $response;
        }
    }

    public function buildExtraParameters(int $hostingId = null): array
    {}
}
