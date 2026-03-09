<?php

namespace WHMCS\Module\Addon\Cloud4Africa\Traits;

use Illuminate\Database\Capsule\Manager as Capsule;

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
