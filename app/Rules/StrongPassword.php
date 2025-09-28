<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    public function __construct(
        protected ?string $username = null,
        protected ?int $minLengthMatch = 3
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->username) {
            return;
        }

        $parts = preg_split('/[^a-zA-Z0-9]/', $this->username);

        foreach ($parts as $part) {
            $part = trim($part);

            if (strlen($part) >= $this->minLengthMatch) {
                for ($i = 0; $i <= strlen($part) - $this->minLengthMatch; $i++) {
                    $substring = substr($part, $i, $this->minLengthMatch);

                    if (stripos($value, $substring) !== false) {
                        $fail('The password must not contain parts of your username.');

                        return;
                    }
                }
            }
        }
    }
}
