<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Tymon\JWTAuth\JWTGuard;

if (! function_exists('jwtGuard')) {
    /**
     * @method \Tymon\JWTAuth\Contracts\JWTSubject|null authenticate()
     * @method \Tymon\JWTAuth\Payload parseToken()
     * @method \Tymon\JWTAuth\Payload getPayload()
     */
    function jwtGuard(): JWTGuard
    {
        /** @var JWTGuard $guard */
        $guard = auth('api');

        return $guard;
    }
}

if (! function_exists('generateOtp')) {
    function generateOtp(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}

if (! function_exists('decryptIfNotNull')) {
    function decryptIfNotNull(?string $value, $model, ?string $attr): ?string
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
    function get_class_name($classname): string
    {
        $pos = strrpos($classname, '\\');

        return $pos !== false
            ? substr($classname, $pos + 1)
            : $classname;
    }
}

if (! function_exists('sanitize')) {
    function sanitize(string $value, bool $escapeHtml = true, bool $lowercase = true): string | false
    {
        if (! mb_check_encoding($value, 'UTF-8')) {
            return false;
        }

        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        $value = trim($value);

        if ($lowercase) {
            $value = mb_strtolower($value, 'UTF-8');
        }

        if ($escapeHtml) {
            $value = htmlspecialchars(
                $value,
                ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE | ENT_DISALLOWED,
                'UTF-8',
                false
            );
        }

        return $value;
    }
}

if (! function_exists('normalize')) {
    function normalize(string $search, string $replace, string $value): string
    {
        return ucwords(str_replace($search, $replace, $value));
    }
}
