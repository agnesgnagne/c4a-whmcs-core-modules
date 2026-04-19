<?php

namespace WHMCS\Cloud4Africa\Traits;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

trait ValidatorTrait
{
    protected function validate(array $data, Assert\Collection $constraints, bool $details = false): array
    {
        $validator = Validation::createValidator();
        
        $violations = $validator->validate($data, $constraints);
        
        $errors = [];
        
        foreach ($violations as $violation) {
            if (! $details) {
                $errors[] = ['message' => $violation->getMessage()];
            } else {
                $errors[$violation->getPropertyPath()][] = $violation->getMessage();
            }
        }
        
        return $errors;
    }
}
