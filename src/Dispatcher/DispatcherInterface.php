<?php

namespace WHMCS\Cloud4Africa\Dispatcher;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;

interface DispatcherInterface
{
    public function dispatch(string $moduleName): string;
    
    public function buildExtraParameters(int $hostingId = null): array;
}
