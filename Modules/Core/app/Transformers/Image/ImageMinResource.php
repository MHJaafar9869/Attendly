<?php

namespace Modules\Core\Transformers\Image;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageMinResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request)
    {
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'imageable' => $resource->imageable?->name,
            'imageable_type' => $resource->imageable_type,
            'disk' => $resource->disk,
            'image_path' => $resource->image_path,
            'image_url' => $resource->image_url,
            'image_mime' => $resource->image_mime,
            'image_alt' => $resource->image_alt,
            'is_flagged' => $resource->is_flagged,
            'flagged_by' => $resource->flagged_by,
            'flagged_at' => $resource->flagged_at,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
            'deleted_at' => $resource->deleted_at,
        ];
    }
}
