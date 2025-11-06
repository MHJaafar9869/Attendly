<?php

namespace Modules\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Translation\PotentiallyTranslatedString;

class ImageMime implements ValidationRule
{
    public function __construct(
        protected array $allowedMimes = ['image/jpeg', 'image/png']
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            $fail('The file is invalid.');

            return;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $value->getRealPath());
        finfo_close($finfo);

        if (! in_array($mimeType, $this->allowedMimes)) {
            $fail('The file must be a valid image: ' . implode(', ', $this->allowedMimes));
        }
    }
}
