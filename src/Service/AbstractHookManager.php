<?php

namespace WHMCS\Cloud4Africa\Service;

use WHMCS\Database\Capsule;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use WHMCS\Cloud4Africa\Repository\WhmcsRepositoryInterface;
use WHMCS\Cloud4Africa\Client\KarajanClientInterface;
use WHMCS\Cloud4Africa\Translation\TranslatorInterface;

abstract class AbstractHookManager
{
    public function __construct(
        protected WhmcsRepositoryInterface $whmcsRepository, 
        protected KarajanClientInterface $karajanClient, 
        protected TranslatorInterface $translator
    ){}
}
