<?php

namespace Modules\Domain\Transformers\Teacher;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request)
    {
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'user' => $resource->user?->name,
            'teacher_code' => $resource->teacher_code,
            'teacherType' => $resource->teacherType?->name,
            'status' => $resource->status?->name,
            'approved_by' => $resource->approved_by,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
            'deleted_at' => $resource->deleted_at,
        ];
    }
}
