<?php

namespace Modules\Core\Transformers\Like;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LikeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request)
    {
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'likeable' => $resource->likeable?->name,
            'likeable_type' => $resource->likeable_type,
            'user' => $resource->user?->name,
            'likes_count' => $resource->likes_count,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }
}
