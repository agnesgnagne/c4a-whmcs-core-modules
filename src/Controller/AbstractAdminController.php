<?php

namespace WHMCS\Cloud4Africa\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use WHMCS\Cloud4Africa\Repository\WhmcsRepositoryInterface;
use WHMCS\Cloud4Africa\Translation\TranslatorInterface;
use WHMCS\Cloud4Africa\Traits\ControllerTrait;
use Smarty\Smarty;

abstract class AbstractAdminController implements ControllerInterface
{
    use ControllerTrait;
    
    /**
     * @param TranslatorInterface $translator
     * @param WhmcsRepositoryInterface $whmcsRepository
     */
    public function __construct(TranslatorInterface $translator, WhmcsRepositoryInterface $whmcsRepository)
    {
        $this->translator = $translator;
        $this->whmcsRepository = $whmcsRepository;
    }
}
