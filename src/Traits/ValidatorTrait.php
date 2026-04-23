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
            $data = $this->sanitize($data, $rules);
            
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
    
    protected function sanitize(array $data, array $rules): array
    {
        $allowedProperties = array_keys($rules);
        
        $unexpected = array_diff(array_keys($data), $allowedProperties);
        
        if (is_array($unexpected) && count($unexpected) > 0) {
            throw new \Exception(json_encode(array_values($unexpected)));
        }
        
        return array_intersect_key($data, array_flip($allowedProperties));
    }
}
