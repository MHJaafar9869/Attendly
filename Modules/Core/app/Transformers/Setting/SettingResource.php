<?php

namespace Modules\Core\Transformers\Setting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request)
    {
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'key' => $resource->key,
            'value' => $resource->value,
            'type' => $resource->type,
            'description' => $resource->description,
            'created_by' => $resource->created_by,
            'updated_by' => $resource->updated_by,
            'deleted_by' => $resource->deleted_by,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
            'deleted_at' => $resource->deleted_at,
        ];
    }
}
