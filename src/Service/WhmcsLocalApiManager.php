<?php

namespace WHMCS\Cloud4Africa\Service;

use WHMCS\Database\Capsule;

class WhmcsLocalApi implements WhmcsLocalApiInterface
{
    private string $admin;

    public function __construct(string $admin = null)
    {
        $this->admin = $admin;
    }

    public function call(string $command, array $postData): array
    {
        return localAPI($command, $postData, $this->admin);
    }
}