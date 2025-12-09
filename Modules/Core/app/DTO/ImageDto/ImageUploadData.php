<?php

declare(strict_types=1);

namespace Modules\Core\DTO\ImageDto;

class ImageUploadData
{
    public function __construct(
        public string $path,
        public string $type,
        public string $disk,
        public string $url,
        public string $mime,
        public ?string $alt = null,
    ) {}

    /**
     * Populate DTO from given parameters.
     */
    public static function make(
        string $path,
        string $type,
        string $disk,
        string $url,
        string $mime,
        ?string $alt = null
    ): self {
        return new self(
            path: $path,
            type: $type,
            disk: $disk,
            url: $url,
            mime: $mime,
            alt: $alt,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'image_path' => $this->path,
            'type' => $this->type,
            'disk' => $this->disk,
            'image_url' => $this->url,
            'image_mime' => $this->mime,
            'image_alt' => $this->alt,
        ];
    }
}
