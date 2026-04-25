<?php

namespace WHMCS\Cloud4Africa\Controller;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use WHMCS\Authentication\CurrentUser;
use WHMCS\ClientArea;
use WHMCS\Cloud4Africa\Service\KarajanManagerInterface;
use WHMCS\Cloud4Africa\Repository\WhmcsRepositoryInterface;
use WHMCS\Cloud4Africa\Translation\TranslatorInterface;
use WHMCS\Cloud4Africa\Service\TemplateManagerInterface;
use WHMCS\Cloud4Africa\Traits\ControllerTrait;
use Smarty\Smarty;

abstract class AbstractClientController implements ControllerInterface
{
    use ControllerTrait;
    
    /** @var KarajanManagerInterface $karajanManager **/
    protected KarajanManagerInterface $karajanManager;
    
    private array $actionMap = [];

    /**
     * @param TranslatorInterface $translator
     * @param WhmcsRepositoryInterface $whmcsRepository
     * @param KarajanManagerInterface $karajanManager
     * @param TemplateManagerInterface $templateManager
     */
    public function __construct(TranslatorInterface $translator, WhmcsRepositoryInterface $whmcsRepository, KarajanManagerInterface $karajanManager, TemplateManagerInterface $templateManager)
    {
        $this->translator = $translator;
        $this->whmcsRepository = $whmcsRepository;
        $this->templateManager = $templateManager;
        $this->karajanManager = $karajanManager;
    }
    
    /**
     * @return HandlerInterface[]
     */
    abstract protected function getHandlers(): array;
    
    private function buildActionMap(): void
    {
        if (!empty($this->actionMap)) {
            return;
        }
        
        foreach ($this->getHandlers() as $handler) {
            $reflection = new ReflectionClass($handler);
            $methods = array_filter(
                $reflection->getMethods(ReflectionMethod::IS_PUBLIC),
                fn($m) => $m->getDeclaringClass()->getName() === $reflection->getName()
                );
            
            foreach ($methods as $method) {
                $this->actionMap[$method->getName()] = $handler;
            }
        }
    }
    
    public function getHandlerFromAction(string $action): ?HandlerInterface
    {
        $this->buildActionMap();
        
        return $this->actionMap[$action] ?? null;
    }
    
    /**
     * @param string $str
     * @return string
     */
    protected function camelCase(string $str): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $str))));
    }
    
    /**
     * Redirect response
     * 
     * @param array<string, mixed> $queryParams
     * @return Response
     */
    protected function redirect(array $queryParams): Response
    {
        if (false === isset($queryParams['m'])){
            return $this->getExceptionResponse(new \Exception($this->translator->trans('error.not_found'), 404));
        }
        
        if (false === isset($queryParams['action'])){
            return $this->getExceptionResponse(new \Exception($this->translator->trans('error.not_found'), 404));
        }
        
        return new RedirectResponse('/index.php?'.http_build_query($queryParams));
    }
}
