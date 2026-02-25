<?php

namespace WHMCS\Cloud4Africa\Service;

use Illuminate\Database\Capsule\Manager as Capsule;
use WHMCS\Cloud4Africa\DTO\Template;

interface TemplateManagerInterface
{
    public function resolveKeyByAction(string $action): ?string;

    public function getTemplate(Capsule $capsule, string $tableName, string $action, string $connectionName = 'default'): ?Template;
}