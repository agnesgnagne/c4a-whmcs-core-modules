<?php

namespace WHMCS\Cloud4Africa\Dispatcher;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use WHMCS\Cloud4Africa\Repository\WhmcsRepositoryInterface;
use WHMCS\Cloud4Africa\Translation\TranslatorInterface;
use WHMCS\Cloud4Africa\Controller\ControllerInterface;
use Illuminate\Database\Capsule\Manager as Capsule;

abstract class AbstractAdminDispatcher implements DispatcherInterface
{
    /** @var Translator $translator **/
    protected $translator;
    
    /** @var WhmcsRepositoryInterface $whmcsRepository **/
    protected WhmcsRepositoryInterface $whmcsRepository;
    
    /** @var TemplateManagerInterface $templateManager **/
    protected TemplateManagerInterface $templateManager;
    
    /** @var ControllerInterface $controller **/
    protected ControllerInterface $controller;
    
    /** @var array $parameters **/
    protected $parameters;
    
    /**
     * @param Translator $translator
     * @param WhmcsRepositoryInterface $whmcsRepository
     * @param TemplateManager $templateManager
     * @param array $parameters
     */
    public function __construct(
        TranslatorInterface $translator,
        WhmcsRepositoryInterface $whmcsRepository,
        ControllerInterface $controller,
        array $parameters
    )
    {
        $this->translator = $translator;
        $this->whmcsRepository = $whmcsRepository;
        $this->controller = $controller;
        $this->parameters = $parameters;
    }
    
    /**
     * Dispatch request.
     *
     * @param string $action
     * @param array $parameters
     *
     * @return Response|string|null
     */
    public function dispatch(string $action, string ?$hostingId = null): Response|string|null
    {
        $this->parameters = array_merge($this->parameters, $this->buildExtraParameters($hostingId));
        
        $controller = $this->getController($this->translator, $this->whmcsRepository);
        
        if (is_callable([$controller, $action])) {
            $response = $controller->$action($this->parameters);
            
            if ($response instanceof Response) {
                return $response->send();
            }
            
            return $response;
        }
    }
    
    public function buildExtraParameters(int ?$hostingId = null): array
    {
        return ['translator' => $this->translator];
    }
    
    public function getController(TranslatorInterface $translator, WhmcsRepositoryInterface $whmcsRepository): ControllerInterface
    {}
}
