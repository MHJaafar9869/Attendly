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
            'slug_name' => $resource->slug_name,
            'email' => $resource->email,
            'phone' => $resource->phone,
            'password' => $resource->password,
            'address' => $resource->address,
            'city' => $resource->city,
            'country' => $resource->country,
            'is_verified' => $resource->is_verified,
            'role' => $resource->role?->name,
            'status' => $resource->status?->name,
            'device' => $resource->device,
            'token_version' => $resource->token_version,
            'otp' => $resource->otp,
            'otp_expires_at' => $resource->otp_expires_at,
            'last_visited_at' => $resource->last_visited_at,
            'email_verified_at' => $resource->email_verified_at,
            'remember_token' => $resource->remember_token,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
            'deleted_at' => $resource->deleted_at,
        ];
    }
}
