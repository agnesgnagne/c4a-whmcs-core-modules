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

trait ControllerTrait
{
    /** @var TranslatorInterface $translator **/
    protected TranslatorInterface $translator;

    /** @var WhmcsRepositoryInterface $whmcsRepository **/
    protected WhmcsRepositoryInterface $whmcsRepository;

    /** @var TemplateManagerInterface $templateManager **/
    protected TemplateManagerInterface $templateManager;
    
    /**
     * @var array
     */
    protected array $templateVars;
    
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
        $smarty = new \Smarty\Smarty();
        $smarty->setCompileDir($compilDir ?: self::getCompileDir());

        foreach ($values as $key => $value) {
            $smarty->assign($key, $value);
        }
        
        $this->setTemplateVars($smarty->getTemplateVars());
        $html = $smarty->fetch($template);

        return new Response($html);
    }
    
    /**
     * @param RequestException $e
     * @param array<string, mixed> $vars
     * @return Response
     */
    protected function getRequestExceptionResponse(RequestException $e, array $vars = []): Response
    {
        $message = null;
        $statusCode = null;
        $vars['moduleName'] = 'c4a_mailbox_carbonio';
        
        unset($vars['accessToken']);
        
        if ($e->hasResponse()) {
            $decoded = json_decode((string) $e->getResponse()->getBody()->getContents(), true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                $message = $decoded['message'];
            }
            
            $statusCode = $e->getResponse()->getStatusCode();
        } else {
            $message = $this->translator->trans('contoller.error.default');
            $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
        }
        
        if (function_exists('logModuleCall')) {
            logModuleCall($vars['moduleName'], __FUNCTION__, $vars, $message);
        }
        
        return new Response(
            $message,
            $statusCode,
            ['Content-Type' => false === empty($vars['queryParams']['ajax']) ? 'application/json' : 'text/html']
        );
    }
    
    /**
     * @param \Exception $e
     * @param array<string, mixed> $vars
     * @return Response
     */
    protected function getExceptionResponse(\Exception $e, array $vars = []): Response
    {
        unset($vars['accessToken']);
        
        if (function_exists('logModuleCall')) {
            logModuleCall($vars['moduleName'], __FUNCTION__, $vars, $e->getMessage());
        }
        
        return new Response(
            $this->translator->trans('contoller.error.default'),
            500,
            ['Content-Type' => false === empty($vars['queryParams']['ajax']) ? 'application/json' : 'text/html']
        );
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
     * @param string $template
     * @param array<string, mixed> $params
     * @return Response
     */
    protected function renderCustomTemplate(string $template, array $params): Response
    {
        require_once __DIR__ . '/../../../../../init.php';
        
        $ca = new ClientArea();
        
        $ca->setPageTitle($params['pagetitle']);
        
        foreach ($params['breadcrumb'] as $page => $title) {
            $ca->addToBreadCrumb($page, $title);
        }
        
        $ca->initPage();
        
        $currentUser = new CurrentUser();
        $authUser = $currentUser->user();
        
        // Check login status
        if ($authUser) {
            $ca->assign('userFullname', $authUser->fullName);
            $selectedClient = $currentUser->client();
            
            if ($selectedClient) {
                $ca->assign('clientInvoiceCount', $selectedClient->invoices()->count());
            }
        } else {
            $ca->assign('userFullname', 'Guest');
        }
        
        foreach ($params['vars'] as $key => $value) {
            $ca->assign($key, $value);
        }
        
        $ca->setTemplate($template);
        
        ob_start();
        $ca->output();
        $html = ob_get_clean();
        
        return new Response($html ?: '');
    }

    /**
     * @return string
     */
    protected static function getCompileDir(): string
    {
        return __DIR__ . '/../../../../../../templates_c';
    }
}
