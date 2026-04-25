<?php

namespace WHMCS\Cloud4Africa\Dispatcher;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use WHMCS\Cloud4Africa\Client\KarajanClient;
use WHMCS\Cloud4Africa\Client\KarajanClientInterface;
use WHMCS\Cloud4Africa\Service\KarajanManagerInterface;
use WHMCS\Cloud4Africa\Repository\WhmcsLocalApiManager;
use WHMCS\Cloud4Africa\Repository\WhmcsRepositoryInterface;
use WHMCS\Cloud4Africa\Translation\TranslatorInterface;
use WHMCS\Cloud4Africa\Service\TemplateManagerInterface;
use WHMCS\Cloud4Africa\Controller\ControllerInterface;
use Illuminate\Database\Capsule\Manager as Capsule;
use WHMCS\Cloud4Africa\Traits\ResponseTrait;

abstract class AbstractKarajanClientDispatcher implements DispatcherInterface
{
    use ResponseTrait;
    
    /** @var TranslatorInterface $translator **/
    protected TranslatorInterface $translator;
    
    /** @var WhmcsRepositoryInterface $whmcsRepository **/
    protected WhmcsRepositoryInterface $whmcsRepository;
    
    /** @var KarajanClientInterface $karajanClient **/
    protected KarajanClientInterface $karajanClient;
    
    /** @var TemplateManagerInterface $templateManager **/
    protected TemplateManagerInterface $templateManager;
    
    /** @var ControllerInterface $controller **/
    protected ControllerInterface $controller;
    
    /** @var array $parameters **/
    protected $parameters;
    
    /**
     * @param Translator $translator
     * @param WhmcsRepositoryInterface $whmcsRepository
     * @param KarajanClientInterface $karajanClient
     * @param TemplateManager $templateManager
     * @param array $parameters
     */
    public function __construct(
        TranslatorInterface $translator,
        WhmcsRepositoryInterface $whmcsRepository,
        KarajanClientInterface $karajanClient,
        TemplateManagerInterface $templateManager,
        ControllerInterface $controller,
        array $parameters
        )
    {
        $this->translator = $translator;
        $this->whmcsRepository = $whmcsRepository;
        $this->templateManager = $templateManager;
        $this->karajanClient = $karajanClient;
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
    public function dispatch(string $action, ?int $hostingId = null): Response|array|null
    {
        if ($hostingId) {
            $hosting = $this->whmcsRepository->findBy(['id' => $hostingId], 'tblhosting');
            
            if (true === empty($hosting)) {
                $this->log([
                    'moduleName' => $vars['moduleName'],
                    'action' => $vars['action'] ?: __FUNCTION__,
                    'request' => $vars,
                    'response' => $this->translator->trans('client_dispatch.error.hosting_not_found')
                ]);
                
                return $this->getExceptionResponse(new \Exception($this->translator->trans('client_dispatch.error.default'), 500), $vars);
            }
            
            $this->parameters['hosting'] = $hosting[0];
            $product = $this->whmcsRepository->findBy(['id' => $hosting[0]['packageid']], 'tblproducts');
            
            if (true === empty($product[0])) {
                $this->log([
                    'moduleName' => $vars['moduleName'],
                    'action' => $vars['action'] ?: __FUNCTION__,
                    'request' => $vars,
                    'response' => $this->translator->trans('client_dispatch.error.hosting_not_found')
                ]);
                
                return $this->getExceptionResponse(new \Exception($this->translator->trans('client_dispatch.error.default'), 500), $vars);
            }
            
            $this->parameters['product'] = $product[0]; // Pass product package to controller
            
            $serviceIdField = $this->whmcsRepository->findOneBy([
                'fieldname' => 'serviceId',
                'relid' => $hosting[0]['packageid']
            ], 'tblcustomfields');
            
            $serviceIdFieldValue = $this->whmcsRepository->findOneBy([
                'fieldid' => $serviceIdField->id,
                'type' => 'product',
                'relid' => $hostingId
            ], 'tblcustomfieldsvalues');
            
            if (true === empty($serviceIdFieldValue->value)) {
                $this->log([
                    'moduleName' => $vars['moduleName'],
                    'action' => $vars['action'] ?: __FUNCTION__,
                    'request' => $vars,
                    'response' => $this->translator->trans('client_dispatch.error.service_id_not_found')
                ]);
                
                return $this->getExceptionResponse(new \Exception($this->translator->trans('client_dispatch.error.default'), 500), $vars);
            }
            
            $token = $this->karajanClient->fetchAuthToken();
            
            $this->parameters['accessToken'] = $token['accessToken'];
            
            try {
                $response = $this->karajanClient->request(
                    'GET',
                    sprintf('%s/orchestrator/v1/rest/services/%s', $this->karajanClient->getBaseUrl(), $serviceIdFieldValue->value),
                    [
                        RequestOptions::HEADERS => [
                            'Authorization' => sprintf('Bearer %s', $this->parameters['accessToken'])
                        ]
                    ]
                    );
            } catch (RequestException $e) {
                $this->log([
                    'moduleName' => $vars['moduleName'],
                    'action' => $vars['action'] ?: __FUNCTION__,
                    'request' => $vars,
                    'response' => $e->getResponse()->getBody()->getContents()
                ]);
                
                return $this->getExceptionResponse(new \Exception($this->translator->trans('client_dispatch.error.default'), 500), $vars);
            }
            
            $service = json_decode($response->getBody()->getContents(), true);
            
            $this->parameters['region'] = $service['region']['displayName'] ?? $service['region']['name'];
            $this->parameters['resourceRecordSets'] = $service['resourceRecordSets'];
            
            foreach ($service['links'] as $link) {
                $this->parameters[$link['rel']] = $link['href'];
            }
            
            $product = json_decode(Capsule::table('tblproducts')->where('id', $hosting[0]['packageid'])->get(), true);
            
            if (true === empty($product[0])) {
                $this->log([
                    'moduleName' => $vars['moduleName'],
                    'action' => $vars['action'] ?: __FUNCTION__,
                    'request' => $vars,
                    'response' => $this->translator->trans('client_dispatch.error.product_not_found')
                ]);
                
                return $this->getExceptionResponse(new \Exception($this->translator->trans('client_dispatch.error.default'), 500), $vars);
            }
            
            $this->parameters['product'] = $product[0];
        }
        
        $this->parameters = array_merge($this->parameters, $this->buildExtraParameters($hostingId));
        
        $response = $this->controller->call($action, $this->parameters);
        
        if ($response instanceof Response) {
            return $response->send();
        }
        
        return $response;
    }
    
    public function buildExtraParameters(?int $hostingId = null): array
    {}
}
