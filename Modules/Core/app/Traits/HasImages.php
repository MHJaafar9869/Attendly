<?php

namespace Modules\Core\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\Core\Models\Image;

trait HasImages
{
    use UploadFile;

    /**
     * Get all images for the model.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get the primary image (e.g., first one).
     */
    public function primaryImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->latestOfMany();
    }

    /**
     * Attach an image to this model.
     */
    public function addImage(string $path, string $disk = 'public', ?string $alt = null): Image
    {
        return $this->images()->create([
            'image_path' => $path,
            'disk' => $disk,
            'image_alt' => $alt,
            'image_url' => $this->fileUrl($path, $disk),
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
