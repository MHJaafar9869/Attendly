<?php

namespace Modules\Core\Transformers\Type;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request)
    {
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'name' => $resource->name,
            'context' => $resource->context,
            'text_color' => $resource->text_color,
            'bg_color' => $resource->bg_color,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }
}
