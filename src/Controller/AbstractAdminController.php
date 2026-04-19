<?php

namespace WHMCS\Cloud4Africa\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use WHMCS\Cloud4Africa\Repository\WhmcsRepositoryInterface;
use WHMCS\Cloud4Africa\Translation\TranslatorInterface;
use WHMCS\Cloud4Africa\Traits\ControllerTrait;
use WHMCS\Cloud4Africa\Service\KarajanManagerInterface;
use Smarty\Smarty;

abstract class AbstractAdminController implements ControllerInterface
{
    use ControllerTrait;
    
    /** @var KarajanManagerInterface $karajanManager **/
    protected KarajanManagerInterface $karajanManager;
    
    /**
     * @param TranslatorInterface $translator
     * @param WhmcsRepositoryInterface $whmcsRepository
     * @param KarajanManagerInterface $karajanManager
     */
    public function __construct(TranslatorInterface $translator, WhmcsRepositoryInterface $whmcsRepository, KarajanManagerInterface $karajanManager)
    {
        $this->translator = $translator;
        $this->whmcsRepository = $whmcsRepository;
        $this->karajanManager = $karajanManager;
    }
    
    /**
     * Redirect response
     *
     * @param array<string, mixed> $queryParams
     * @return Response
     */
    protected function redirect(array $queryParams): Response
    {
        if (false === isset($queryParams['module'])){
            return $this->getExceptionResponse(new \Exception($this->translator->trans('error.not_found'), 404));
        }
        
        return new RedirectResponse('/admin/addonmodules.php?'.http_build_query($queryParams));
    }
}
