<?php

namespace WHMCS\Cloud4Africa\Service;

use WHMCS\Database\Capsule;

class WhmcsLocalApiManager implements WhmcsLocalApiInterface
{
    public function call(string $command, array $postData): array
    {
        return localAPI($command, $postData);
    }
}