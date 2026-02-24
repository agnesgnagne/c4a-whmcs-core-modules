<?php

namespace WHMCS\Cloud4Africa\Dispatcher;

use Symfony\Component\HttpFoundation\Response;

interface DispatcherInterface
{
    public function dispatch(string $action, string $hostingId = null): Response|array|null;
    
    public function buildExtraParameters(int $hostingId = null): array;
}
