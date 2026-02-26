<?php

namespace WHMCS\Cloud4Africa\Translation;

interface TranslatorInterface
{
    /**
     * @param string $name
     * @param string $locale
     * @param array<string, mixed> $parameters
     * @param string|null $translationDir
     */
    public function trans(string $name, string $locale = 'french', array $parameters = []): ?string;
}