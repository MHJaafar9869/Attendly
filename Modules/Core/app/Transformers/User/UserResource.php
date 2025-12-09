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
            'first_name' => $resource->first_name,
            'last_name' => $resource->last_name,
            'fullname' => $resource->fullname,
            'slug_name' => $resource->slug_name,
            'email' => $resource->email,
            'roles' => $this->whenLoaded('roles', $resource->getRoles()),
            'permissions' => $this->whenLoaded('permissions', $resource->getPermissions()),
            'status' => $resource->status?->name,
            'last_visited' => $resource->last_visited_at->diffForHumans(),
            'email_verified_at' => $resource->email_verified_at->format('dMy H:i'),
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
            'deleted_at' => $resource->deleted_at,
        ];
    }
}
