<?php

namespace WHMCS\Cloud4Africa\Traits;

use Illuminate\Database\Schema\Blueprint;

trait ConfigTemplateMockTrait
{
    use CapsuleMockTrait;

    protected function initTemplate($tableName, string $key, string $tplPath): void
    {
        $capsule = $this->getCapsule();
        $this->initConfig($tableName, $key, $tplPath);
    }

    protected function initConfig(string $tableName, string $key = null, string $value = null, string $description = null): void
    {
        $capsule = $this->getCapsule();
        if (!$capsule->schema()->hasTable($tableName)) {
            $capsule->schema()->create($tableName, function (Blueprint $table) {
                $table->increments('id');
                $table->text('key')->unique();
                $table->text('description')->nullable();
                $table->longText('value');
            });
        }

        if ($key && $value) {
            $capsule::table($tableName)->insert([
                'key' => $key,
                'value' => $value,
                'description' => $description
            ]);
        }
    }
}
