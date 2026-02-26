<?php

namespace WHMCS\Cloud4Africa\Translation;

abstract class AbstractTranslator implements TranslatorInterface
{
    /** @var string $translationDir **/
    private string $translationDir;

    public function __construct(?string $translationDir)
    {
        $this->translationDir = false === empty($translationDir) ? $translationDir : __DIR__ . '/../../lang';   
    }

    /**
     * @param string $name
     * @param string $locale
     * @param array<string, mixed> $parameters
     * @param string $translationDir
     * @return string|NULL
     */
    public function trans(string $name, string $locale = 'french', array $parameters = []): ?string
    {
        include $this->translationDir . '/' . $locale . '.php';
        $translation = false === empty($LANG[$name]) ? $LANG[$name] : $name;

        foreach ($parameters as $key => $value) {
            $translation = str_replace($key, $value, $translation);
        }

        return $translation;
    }
}