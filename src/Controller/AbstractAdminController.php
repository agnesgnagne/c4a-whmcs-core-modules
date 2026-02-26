<?php

namespace WHMCS\Cloud4Africa\Controller;

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
     * @param RequestException $e
     * @param array<string, mixed> $vars
     * @return Response
     */
    protected function getRequestExceptionResponse(RequestException $e, array $vars = []): Response
    {
        $message = null;
        $statusCode = null;

        unset($vars['accessToken']);

        if ($e->hasResponse()) {
            $message = $this->extractApiErrorMessage((string) $e->getResponse()->getBody()->getContents());
            $statusCode = $e->getResponse()->getStatusCode();
        } else {
            $message = $e->getMessage() ? $e->getMessage() : $this->translator->trans('mailbox_carbonio.error.default');
            $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
        }
        logModuleCall('c4a_mailbox_carbonio', __FUNCTION__, $vars, $message);

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

        logModuleCall('c4a_mailbox_carbonio', __FUNCTION__, $vars, $e->getMessage());

        return new Response(
            $e->getMessage() ? $e->getMessage() : $this->translator->trans('mailbox_carbonio.error.default'),
            method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500,
            ['Content-Type' => false === empty($vars['queryParams']['ajax']) ? 'application/json' : 'text/html']
        );
    }
}
