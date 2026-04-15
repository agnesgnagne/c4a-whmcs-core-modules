<?php

namespace WHMCS\Cloud4Africa\Service;

use WHMCS\Cloud4Africa\DTO\Template;
use WHMCS\Cloud4Africa\Repository\WhmcsRepositoryInterface;

abstract class AbstractTemplateManager implements TemplateManagerInterface
{
    /**
     * @var WhmcsRepositoryInterface
     */
    protected WhmcsRepositoryInterface $whmcsRepository;
    
    /**
     * @var string
     */
    protected string $tableName;
    
    /**
     * @var string
     */
    protected string $action;
    
    /**
     * @var string
     */
    protected string $connectionName;
    
    /**
     * @param WhmcsRepositoryInterface $whmcsRepository
     * @param string $tableName
     * @param string $action
     * @param string $connectionName
     */
    public function __construct(WhmcsRepositoryInterface $whmcsRepository, string $tableName, ?string $action = null, ?string $connectionName = 'default')
    {
        $this->whmcsRepository = $whmcsRepository;
        $this->tableName = $tableName;
        $this->action = $action;
        $this->connectionName = $connectionName;
    }
    
    public function getTemplate(?string $key = null) :?Template
    {
        if (! $key) {
            $key = $this->resolveKey();
        }
        
        $template = $this->whmcsRepository->findOneBy($this->tableName, [['field' => 'key', 'value' => $key]]);
        
        if (count($template) == 0) {
            return null;
        }
        
        return new Template($template['key'], $template['value']);
    }
}
