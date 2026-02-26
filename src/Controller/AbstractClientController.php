<?php

namespace WHMCS\Cloud4Africa\Controller;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use WHMCS\Authentication\CurrentUser;
use WHMCS\ClientArea;
use WHMCS\Cloud4Africa\Client\KarajanClientInterface;
use WHMCS\Cloud4Africa\Repository\WhmcsRepositoryInterface;
use WHMCS\Cloud4Africa\Translation\TranslatorInterface;
use WHMCS\Cloud4Africa\Service\TemplateManagerInterface;

abstract class AbstractClientController implements ControllerInterface
{
    /** @var TranslatorInterface $translator **/
    protected $translator;

    /** @var WhmcsRepositoryInterface $whmcsRepository **/
    protected WhmcsRepositoryInterface $whmcsRepository;

    /** @var KarajanClientInterface $karajanClient **/
    protected KarajanClientInterface $karajanClient;

    /** @var TemplateManagerInterface $templateManager **/
    protected TemplateManagerInterface $templateManager;

    /**
     * @param TranslatorInterface $translator
     * @param WhmcsRepositoryInterface $whmcsRepository
     * @param KarajanClientInterface $karajanClient
     * @param TemplateManagerInterface $templateManager
     */
    public function __construct(TranslatorInterface $translator, WhmcsRepositoryInterface $whmcsRepository, KarajanClientInterface $karajanClient, TemplateManagerInterface $templateManager)
    {
        $this->translator = $translator;
        $this->whmcsRepository = $whmcsRepository;
        $this->templateManager = $templateManager;
        $this->karajanClient = $karajanClient;
    }

    /**
     * @param string $template
     * @param array<string, mixed> $values
     * @return Response
     */
    protected function getResponse(string $template, array $values = []): Response
    {
        $smarty = new \Smarty();
        $smarty->setCompileDir(self::getCompileDir());

        foreach ($values as $key => $value) {
            $smarty->assign($key, $value);
        }

        $html = $smarty->fetch($template);

        return new Response($html);
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
