<?php

namespace Modules\Core\Transformers\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $resource->email,
            'phone' => $resource->phone,
            'address' => $resource->address,
            'role' => $resource->role?->name,
            'is_active' => $resource->is_active,
            'status' => $resource->status?->name,
            'remember_token' => $resource->remember_token,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
        ];
    }
}
