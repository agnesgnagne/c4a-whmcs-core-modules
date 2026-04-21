<?php

namespace WHMCS\Cloud4Africa\Traits;

use Illuminate\Validation\Factory;
use Illuminate\Translation\Translator;
use Illuminate\Translation\FileLoader;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Validation\ValidationException;

trait ValidatorTrait
{
    /**
     * 
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $attributes
     * @param string $locale
     * @param string $translationDirectory
     * @return array
     */
    protected function validate(
        array $data = [],
        array $rules = [],
        array $messages = [],
        array $attributes = [],
        ?string $locale = 'fr',
        ?string $translationDirectory = null
        ): array
    {
        $filesystem = new Filesystem();
        
        $loader = new FileLoader(
            $filesystem,
            $translationDirectory ?? __DIR__.'/../../lang'
            );
        
        $translator = new Translator($loader, $locale);
        
        $factory = new Factory($translator);
        
        $validator = $factory->make($data, $rules, $messages, $attributes);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return $validator->validated();
    }
}
