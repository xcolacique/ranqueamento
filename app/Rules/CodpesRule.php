<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CodpesRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $values = explode(',',$value);
        foreach($values as $v) {
            if (!(is_numeric(trim($v)))) {
                $fail("Número USP - {$v} - precisa ser um número inteiro");
            }
        }
    }
}
