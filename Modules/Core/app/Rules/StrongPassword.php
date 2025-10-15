<?php

namespace Modules\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class StrongPassword implements ValidationRule
{
    public function __construct(
        protected ?string $name = null,
        protected ?int $minLengthMatch = 4
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->name) {
            return;
        }

        $parts = preg_split('/[^a-zA-Z0-9]/', $this->name);

        foreach ($parts as $part) {
            $part = trim($part);

            if (strlen($part) >= $this->minLengthMatch) {
                for ($i = 0; $i <= strlen($part) - $this->minLengthMatch; $i++) {
                    $substring = substr($part, $i, $this->minLengthMatch);

                    if (stripos($value, $substring) !== false) {
                        $fail('The password must not contain parts of your name.');

                        return;
                    }
                }
            }
        }
    }
}
