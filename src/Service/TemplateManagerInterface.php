<?php

namespace WHMCS\Cloud4Africa\Service;

interface TemplateManagerInterface
{
    public function resolveKeyByAction(string $action): ?string;

    public function getTemplate(string $tableName, string $connectionName = 'default'): ?array;
}