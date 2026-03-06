<?php

namespace WHMCS\Cloud4Africa\Translation;

abstract class AbstractTranslator implements TranslatorInterface
{
    /** @var string $translationDir **/
    private string $translationDir;

    public function __construct(?string $translationDir)
    {
        $this->translationDir = $translationDir;   
    }

    /**
     * @param string $name
     * @param string $locale
     * @param array<string, mixed> $parameters
     * @param string $translationDir
     * @return string|\Exception
     */
    public function trans(string $name, string $locale = 'french', array $parameters = []): string|\Exception
    {
        $translationFile = ($this->getCustomTranslationDir() ?: $this->getDefaultTranslationDir()) . '/' . $locale . '.php';
        
        if (! file_exists($translationFile)) {
            throw new \Exception('Translation file not exists');
        }
        
        include $translationFile;
        
        $translation = false === empty($LANG[$name]) ? $LANG[$name] : $name;
        
        if (count($parameters) > 0) {
            foreach ($parameters as $key => $value) {
                $translation = str_replace($key, $value, $translation);
            }
        }

        return $translation;
    }
    
    public function getCustomTranslationDir(): ?string
    {
        return $this->translationDir;
    }
    
    public function getDefaultTranslationDir(): string
    {
        return realpath(__DIR__ . '/../../lang');
    }
}