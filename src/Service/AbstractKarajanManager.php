<?php

namespace WHMCS\Cloud4Africa\Service;

use WHMCS\Cloud4Africa\DTO\Template;
use WHMCS\Cloud4Africa\Repository\WhmcsRepositoryInterface;

abstract class AbstractKarajanManager implements KarajanManagerInterface
{
    /** @var KarajanClientInterface $karajanClient **/
    protected KarajanClientInterface $karajanClient;
    
    public function __construct(KarajanClientInterface $karajanClient)
    {
        $this->karajanClient = $karajanClient;
    }

    protected function getKarajanClient(): KarajanClientInterface
    {
        return $this->karajanClient;
    }
}
