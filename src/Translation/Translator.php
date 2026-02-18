<?php

namespace WHMCS\Cloud4Africa\Translation;

class Translator implements TranslatorInterface
{
    public function trans($name, $locale = null, array $parameters = [], $translationDir = null): ?string
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
