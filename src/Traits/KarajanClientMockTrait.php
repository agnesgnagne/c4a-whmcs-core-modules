<?php

namespace WHMCS\Module\Addon\Cloud4Africa\Traits;

trait KarajanClientTrait
{
    use AssertMockTrait;
    use KarajanManagerResponseTrait;

    protected array $vars;

    protected function makeAuthToken()
    {
        return [
            'accessToken' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWUsImlhdCI6MTUxNjIzOTAyMn0.KMUFsIDTnFmyG3nMiGM6H9FNFUROf3wh7SmqJp-QV30',
            'projectId' => '29b9bd07-7b05-40e2-bb24-fa639a96794f'
        ];
    }

    protected function makeKarajanServer()
    {
        return [
            'accesshash' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6Ik',
            'username' => 'c4a',
            'password' => 'c4a',
            'secure' => 'on',
            'projectId' => '29b9bd07-7b05-40e2-bb24-fa639a96794f'
        ];
    }

    protected function mockWhmcsRepository()
    {
        $this->whmcsRepository->method('findValidKarajanToken')->willReturn($this->makeAuthToken());
        $this->whmcsRepository->method('findKarajanServer')->willReturn($this->makeKarajanServer());
    }

    protected function makeVars(array $extraVars)
    {
        return array_merge_recursive($extraVars, [
            'uriParams' => [
                'serviceId' => '1'
            ],
            'resourceWebUrl' => 'https://mail.dev.veone.net',
            'resourceAPIUrl' => 'https://api.waf.c4a.abj.karajan.c4a.dev.veone.net/carbonio',
            'accessToken' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWUsImlhdCI6MTUxNjIzOTAyMn0.KMUFsIDTnFmyG3nMiGM6H9FNFUROf3wh7SmqJp-QV30'
        ]);
    }
}
