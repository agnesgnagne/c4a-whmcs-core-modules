<?php

namespace WHMCS\Cloud4Africa\Service;

use WHMCS\Cloud4Africa\DTO\Template;
use Illuminate\Database\Capsule\Manager as Capsule;

abstract class AbstractTemplateManager implements TemplateManagerInterface
{
    public function __invoke(Capsule $capsule, string $tableName, string $action): ?Template
    {
        return $this->getTemplate($capsule, $tableName, $action);
    }

    public function getTemplate(Capsule $capsule, string $tableName, string $action, string $connectionName = 'default') :?Template
    {
        $key = $this->resolveKeyByAction($action);
        $template = $this->capsule->getConnection($connectionName)->table($this->tableName)->where('key', $key)->first();
        
        if (! $template->value) {
            return null;
        }
        
        return new Template(
            key: $template->key,
            value: $template->value
        );
    }
}
