<?php

namespace WHMCS\Cloud4Africa\Translation;

class Translator implements TranslatorInterface
{
    /**
     * @param string $name
     * @param string $locale
     * @param array<string, mixed> $parameters
     * @param string $translationDir
     * @return string|NULL
     */
    public function trans(string $name, string $locale = 'french', array $parameters = [], string $translationDir = null): ?string
    {
        $locale = false === empty($locale) ? $locale : 'french';
        $translationDir = false === empty($translationDir) ? $translationDir : __DIR__ . '/../../lang';

        include $translationDir . '/' . $locale . '.php';
        $translation = false === empty($LANG[$name]) ? $LANG[$name] : $name;

        foreach ($parameters as $key => $value) {
            $translation = str_replace($key, $value, $translation);
        }

        return $translation;
    }
}
