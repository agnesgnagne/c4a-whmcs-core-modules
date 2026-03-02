<?php

namespace namespace WHMCS\Cloud4Africa\Tests\Traits;

use GuzzleHttp\Psr7\Response;

trait ConfigTemplateMockTrait
{
    use CapsuleMockTrait;
    
    protected function initTemplate($tableName, string $key, string $tplPath): void
    {
        $capsule = $this->getCapsule();
        $this->initConfig($capsule, $tableName, $key, $tplPath);

        $mockSmarty = $this->createMock(\Smarty::class);
        $mockSmarty->method('assign')->willReturn(null);
        $mockSmarty->method('setCompileDir')->willReturn(null);
        $mockSmarty->method('fetch')->willReturn(null);
    }

    protected function initConfig(Capsule $capsule, string $tableName, string $key = null, string $value = null, string $description = null): void
    {
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