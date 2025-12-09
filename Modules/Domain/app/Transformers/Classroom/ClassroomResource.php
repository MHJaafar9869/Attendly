<?php

namespace Modules\Domain\Transformers\Classroom;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassroomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request)
    {
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'teacher' => $resource->teacher?->name,
            'subject' => $resource->subject?->name,
            'start_at' => $resource->start_at,
            'end_at' => $resource->end_at,
            'lat' => $resource->lat,
            'lng' => $resource->lng,
            'radius' => $resource->radius,
            'created_by' => $resource->created_by,
            'updated_by' => $resource->updated_by,
            'deleted_by' => $resource->deleted_by,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
            'deleted_at' => $resource->deleted_at,
        ];
    }
}
