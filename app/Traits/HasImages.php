<?php

namespace App\Traits;

use Modules\Core\Models\Image;

trait HasImages
{
    use UploadFile;

    /**
     * Get all images for the model.
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get the primary image (e.g., first one).
     */
    public function primaryImage()
    {
        return $this->morphOne(Image::class, 'imageable')->latestOfMany();
    }

    /**
     * Attach an image to this model.
     */
    public function addImage(string $path, string $mime, string $disk = 'public', ?string $alt = null): Image
    {
        return $this->images()->create([
            'image_path' => $path,
            'disk' => $disk,
            'image_alt' => $alt,
            'image_url' => $this->fileUrl($path, $disk),
            'image_mime' => $mime,
        ]);
    }

    /**
     * Remove a specific image.
     */
    public function removeImage(Image $image): void
    {
        $image->delete();
    }

    /**
     * Remove all images from this model.
     */
    public function clearImages(): void
    {
        $this->images()->delete();
    }
}
