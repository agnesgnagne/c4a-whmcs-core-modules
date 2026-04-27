<?php

namespace WHMCS\Cloud4Africa\Dispatcher;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use WHMCS\Cloud4Africa\Repository\WhmcsLocalApiManager;
use WHMCS\Cloud4Africa\Repository\WhmcsRepositoryInterface;
use WHMCS\Cloud4Africa\Translation\TranslatorInterface;
use WHMCS\Cloud4Africa\Service\TemplateManagerInterface;
use WHMCS\Cloud4Africa\Controller\ControllerInterface;
use Illuminate\Database\Capsule\Manager as Capsule;

abstract class AbstractClientDispatcher implements DispatcherInterface
{
    /** @var TranslatorInterface $translator **/
    protected TranslatorInterface $translator;
    
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
        TemplateManagerInterface $templateManager,
        ControllerInterface $controller,
        array $parameters
        )
    {
        $this->translator = $translator;
        $this->whmcsRepository = $whmcsRepository;
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
    public function dispatch(string $action, ?int $hostingId = null): Response|array|null
    {
        $response = $this->controller->call($action, $this->parameters);
        
        if ($response instanceof Response) {
            return $response->send();
        }
        
        return $response;
    }
    
    public function buildExtraParameters(?int $hostingId = null): array
    {}
}
