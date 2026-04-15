<?php

namespace WHMCS\Cloud4Africa\DTO;

class Template
{
    public string $key;
    public string $value;
    
    public function __construct(string $key, string $value)
    {
        $this->key = $key;
        $this->value = $value;
    }
    
    public function getKey(): string
    {
        return $this->key;
    }
    
    public function getValue(): string
    {
        return $this->value;
    }
}