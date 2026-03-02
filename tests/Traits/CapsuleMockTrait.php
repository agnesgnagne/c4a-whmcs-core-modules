<?php

namespace namespace WHMCS\Cloud4Africa\Tests\Traits;

use GuzzleHttp\Psr7\Response;

trait CapsuleMockTrait
{
    protected function getCapsule()
    {
        $capsule = new Capsule();

        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        return $capsule;
    }
}