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

trait LoggerTrait
{
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
