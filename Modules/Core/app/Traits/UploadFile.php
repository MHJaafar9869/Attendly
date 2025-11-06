<?php

namespace Modules\Core\Traits;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait UploadFile
{
    public function uploadFile(UploadedFile $file, string $folder = 'uploads', string $disk = 'public'): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = Storage::disk($disk)->putFileAs($folder, $file, $filename);

        return $path;
    }

    public function deleteFile(string $path, string $disk = 'public'): void
    {
        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    public function fileUrl(string $path, string $disk = 'public'): string
    {
        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk($disk);

        return $storage->url($path);
    }

    public function getMime(UploadedFile $file): string
    {
        return $file->getMimeType();
    }
}
