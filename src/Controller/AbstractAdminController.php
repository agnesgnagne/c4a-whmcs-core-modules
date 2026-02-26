<?php

namespace WHMCS\Cloud4Africa\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use WHMCS\Cloud4Africa\Repository\WhmcsRepositoryInterface;
use WHMCS\Cloud4Africa\Translation\TranslatorInterface;

abstract class AbstractAdminController implements ControllerInterface
{
    /** @var TranslatorInterface $translator **/
    protected TranslatorInterface $translator;

    /** @var WhmcsRepositoryInterface $whmcsRepository **/
    protected WhmcsRepositoryInterface $whmcsRepository;

    /**
     * @param TranslatorInterface $translator
     * @param WhmcsRepositoryInterface $whmcsRepository
     */
    public function __construct(TranslatorInterface $translator, WhmcsRepositoryInterface $whmcsRepository)
    {
        $this->translator = $translator;
        $this->whmcsRepository = $whmcsRepository;
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
     * @return string
     */
    protected static function getCompileDir(): string
    {
        return __DIR__ . '/../../../../../../templates_c';
    }
}
