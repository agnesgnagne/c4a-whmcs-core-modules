<?php

namespace WHMCS\Cloud4Africa\Util;

class Translator
{
    public function trans($name, $locale = null, array $parameters = [], $translationDir = null)
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
