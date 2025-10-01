<?php

namespace Modules\Core\Transformers\Favorite;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteMinResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request)
    {
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'favoriteable' => $resource->favoriteable?->name,
            'favoriteable_type' => $resource->favoriteable_type,
            'collection' => $resource->collection,
            'user' => $resource->user?->name,
            'favorites_count' => $resource->favorites_count,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }
}
