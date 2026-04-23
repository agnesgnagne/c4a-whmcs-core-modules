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

trait TemplateTrait
{
    use ResponseTrait;
    
    /** @var TranslatorInterface $translator **/
    protected TranslatorInterface $translator;
    
    /** @var WhmcsRepositoryInterface $whmcsRepository **/
    protected WhmcsRepositoryInterface $whmcsRepository;
    
    /** @var TemplateManagerInterface $templateManager **/
    protected TemplateManagerInterface $templateManager;
    
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
