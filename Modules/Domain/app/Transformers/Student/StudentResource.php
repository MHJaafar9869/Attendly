<?php

namespace Modules\Domain\Transformers\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'student_code' => $resource->student_code,
            'hashedNational' => $resource->hashedNational?->name,
            'gender' => $resource->gender,
            'academic_year' => $resource->academic_year,
            'section' => $resource->section,
            'phone' => $resource->phone,
            'secondary_phone' => $resource->secondary_phone,
            'address' => $resource->address,
            'city' => $resource->city,
            'governorate' => $resource->governorate?->name,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
            'deleted_at' => $resource->deleted_at,
        ];
    }
}
