<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Modules\Core\Models\User;

if (! function_exists('sanctumUser')) {
    /**
     * Get the authenticated user via Sanctum guard
     */
    function sanctumUser(): ?User
    {
        /** @var User|null $guard */
        $guard = auth('sanctum')?->user();

        return $guard;
    }
}

if (! function_exists('generateOtp')) {
    function generateOtp(int $digits = 6): string
    {

        $digits = max(1, $digits); // Ensure digits is at least 1

        $max = (10 ** $digits) - 1; // Maximum number for given digits

        // Generate random integer and pad with leading zeros
        return str_pad((string) random_int(0, $max), $digits, '0', STR_PAD_LEFT);
    }
}

if (! function_exists('decryptIfNotNull')) {
    function decryptIfNotNull(?string $value, object $model, ?string $attr): ?string
    {
        try {
            $decrypted = $value ? Crypt::decrypt($value) : null;
        } catch (DecryptException $e) {
            if (app()->environment('local')) {
                logger()->warning("Failed to decrypt {$attr} in model " . get_class($model), [
                    'id' => $model->id ?? null,
                    'error' => $e->getMessage(),
                ]);
            }

            return null;
        }

        return $decrypted;
    }
}

if (! function_exists('get_class_name')) {
    function get_class_name(string $classname): string
    {
        $pos = strrpos($classname, '\\');

        return $pos !== false
            ? substr($classname, $pos + 1)
            : $classname;
    }
}

if (! function_exists('sanitize')) {
    function sanitize(string $value, bool $lowercase = false): string
    {
        if (! mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }

        $value = trim($value);

        if ($lowercase) {
            $value = mb_strtolower($value, 'UTF-8');
        }

        return $value;
    }
}

if (! function_exists('escape_html')) {
    function escape_html(string $value): string
    {
        if (! mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }

        return htmlspecialchars(
            $value,
            ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE | ENT_DISALLOWED,
            'UTF-8',
            false
        );
    }
}

if (! function_exists('normalize')) {
    /**
     * @param  string|array<int, string>  $search
     * @param  string|array<int, string>  $replace
     */
    function normalize(array | string $search, array | string $replace, string $value): string
    {
        return ucwords(str_replace($search, $replace, $value));
    }
}
