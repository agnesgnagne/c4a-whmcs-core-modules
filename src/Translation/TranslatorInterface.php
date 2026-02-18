<?php

namespace WHMCS\Cloud4Africa\Translation;

interface TranslatorInterface
{
    public function trans($name, $locale = null, array $parameters = [], $translationDir = null): ?string;
}
