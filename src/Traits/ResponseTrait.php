<?php

namespace WHMCS\Cloud4Africa\Traits;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use WHMCS\Authentication\CurrentUser;
use WHMCS\ClientArea;
use WHMCS\Cloud4Africa\Repository\WhmcsRepositoryInterface;
use WHMCS\Cloud4Africa\Translation\TranslatorInterface;
use WHMCS\Cloud4Africa\Service\TemplateManagerInterface;
use Smarty\Smarty;

trait ResponseTrait
{
    /** @var TranslatorInterface $translator **/
    protected TranslatorInterface $translator;
    
    /**
     * @var array
     */
    protected array $templateVars = [];
    
    /**
     * @param array $templateVars
     * @return array
     */
    public function setTemplateVars(array $templateVars): array
    {
        return $this->templateVars = $templateVars;
    }
    
    /**
     * @return array
     */
    public function getTemplateVars(): array
    {
        return $this->templateVars;
    }
    
    
    /**
     * @param string $template
     * @param array<string, mixed> $values
     * @return Response
     */
    protected function getResponse(string $template, array $values = [], ?string $compilDir = null): Response
    {
        $smarty = class_exists(Smarty::class) ? new \Smarty\Smarty() : new \Smarty();
        $smarty->setCompileDir($compilDir ?: self::getCompileDir());
        
        foreach ($values as $key => $value) {
            $smarty->assign($key, $value);
        }
        
        $this->setTemplateVars($smarty->getTemplateVars());
        $html = $smarty->fetch($template);
        
        return new Response($html);
    }
    
    /**
     * @param RequestException|\Exception $e
     * @param array<string, mixed> $vars
     * @return Response
     */
    protected function getExceptionResponse(RequestException|\Exception $e, array $vars = []): Response
    {
        $message = null;
        $statusCode = null;
        
        unset($vars['accessToken']);
        
        if (method_exists($e, 'hasResponse') && $e->hasResponse()) {
            $decoded = json_decode((string) $e->getResponse()->getBody()->getContents(), true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                $message = $decoded['message'];
                $statusCode = $e->getResponse()->getStatusCode();
            } else {
                $message = $this->translator->trans('controller.error.default');
                $statusCode = method_exists($e, 'getCode') ? $e->getCode() : 500;
            }
        } else {
            $message = method_exists($e, 'getMessage') ? $e->getMessage(): $this->translator->trans('controller.error.default');
            $statusCode = method_exists($e, 'getCode') ? $e->getCode() : 500;
        }
        
        $this->log([
            'moduleName' => $vars['moduleName'],
            'action' => $vars['action'] ?: __FUNCTION__,
            'request' => $vars,
            'response' => $message
        ]);
        
        return new Response(
            $message,
            $statusCode,
            ['Content-Type' => false === empty($vars['queryParams']['ajax']) ? 'application/json' : 'text/html']
            );
    }
    
    /**
     * Redirect response
     *
     * @param array<string, mixed> $queryParams
     * @param string $baseUrl
     * @return Response
     */
    protected function redirect(array $queryParams, string $baseUrl = '/index.php'): Response
    {
        if (false === isset($queryParams['m'])){
            return $this->getExceptionResponse(new \Exception($this->translator->trans('error.not_found'), 404));
        }
        
        if (false === isset($queryParams['action'])){
            return $this->getExceptionResponse(new \Exception($this->translator->trans('error.not_found'), 404));
        }
        
        return new RedirectResponse($baseUrl.'?'.http_build_query($queryParams));
    }
    
    /**
     * @param array<string, mixed> $params
     */
    protected function log(array $params): void
    {
        if (function_exists('logModuleCall')) {
            logModuleCall($params['moduleName'], $params['action'], $params['request'], $params['response']);
        }
        
        return;
    }
}
