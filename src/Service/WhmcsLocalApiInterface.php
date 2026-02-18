<?php

namespace WHMCS\Cloud4Africa\Service;

interface WhmcsLocalApiInterface
{
    public function call(string $command, array $postData): array;
}