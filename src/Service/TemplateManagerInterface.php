<?php

namespace WHMCS\Cloud4Africa\Service;

use WHMCS\Cloud4Africa\DTO\Template;

interface TemplateManagerInterface
{
    public function resolveKey(): ?string;

    public function getTemplate(?string $key = null): ?Template;
}