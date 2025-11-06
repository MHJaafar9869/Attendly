<?php

namespace Modules\Core\DTO\User;

class ImageUploadData
{
    public function __construct(
        public readonly int | string $userId,
        public readonly string $path,
        public readonly string $type,
        public readonly string $disk,
        public readonly string $url,
        public readonly string $mime,
        public readonly ?string $alt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['userId'],
            path: $data['path'],
            type: $data['type'],
            disk: $data['disk'],
            url: $data['url'],
            mime: $data['mime'],
            alt: $data['alt'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'userId' => $this->userId,
            'path' => $this->path,
            'type' => $this->type,
            'disk' => $this->disk,
            'url' => $this->url,
            'mime' => $this->mime,
            'alt' => $this->alt,
        ];
    }

    public static function fromDTO(ImageUploadData $data): self
    {
        return new self(
            userId: $data->userId,
            path: $data->path,
            type: $data->type,
            disk: $data->disk,
            url: $data->url,
            mime: $data->mime,
            alt: $data->alt
        );
    }
}
