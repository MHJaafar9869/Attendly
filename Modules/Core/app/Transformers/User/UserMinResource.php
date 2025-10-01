<?php

namespace Modules\Core\Transformers\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserMinResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request)
    {
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'first_name' => $resource->first_name,
            'last_name' => $resource->last_name,
            'slug_name' => $resource->slug_name,
            'email' => $resource->email,
            'phone' => $resource->phone,
            'address' => $resource->address,
            'city' => $resource->city,
            'country' => $resource->country,
            'is_verified' => $resource->is_verified,
            'role' => $resource->role?->name,
            'status' => $resource->status?->name,
            'device' => $resource->device,
            'last_visited_at' => $resource->last_visited_at,
            'email_verified_at' => $resource->email_verified_at,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
            'deleted_at' => $resource->deleted_at,
        ];
    }
}
