<?php

namespace WHMCS\Cloud4Africa\Controller;

use WHMCS\Cloud4Africa\Client\KarajanClientInterface;
use WHMCS\Cloud4Africa\Repository\WhmcsRepositoryInterface;
use WHMCS\Cloud4Africa\Translation\TranslatorInterface;
use WHMCS\Cloud4Africa\Service\TemplateManagerInterface;

abstract class AbstractController implements ControllerInterface
{
    /** @var TranslatorInterface $translator **/
    private $translator;

    /** @var WhmcsRepositoryInterface $whmcsRepository **/
    private WhmcsRepositoryInterface $whmcsRepository;

    /** @var KarajanClientInterface $karajanClient **/
    private KarajanClientInterface $karajanClient;

    /** @var TemplateManagerInterface $templateManager **/
    private TemplateManagerInterface $templateManager;

    private $smartyClass;

    /**
     * @param Translator $translator
     * @param RepositoryInterface $repository
     */
    public function __construct(TranslatorInterface $translator, WhmcsRepositoryInterface $whmcsRepository, KarajanClientInterface $karajanClient, TemplateManagerInterface $templateManager)
    {
        $this->translator = $translator;
        $this->whmcsRepository = $whmcsRepository;
        $this->templateManager = $templateManager;
        $this->karajanClient = $karajanClient;
    }
}
