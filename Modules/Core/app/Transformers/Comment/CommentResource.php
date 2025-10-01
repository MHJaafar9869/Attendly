<?php

namespace Modules\Core\Transformers\Comment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request)
    {
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'commentable' => $resource->commentable?->name,
            'commentable_type' => $resource->commentable_type,
            'content' => $resource->content,
            'user' => $resource->user?->name,
            'is_flagged' => $resource->is_flagged,
            'flagged_by' => $resource->flagged_by,
            'flagged_at' => $resource->flagged_at,
            'parent' => $resource->parent?->name,
            'created_at' => $resource->created_at,
            'updated_at' => $resource->updated_at,
            'deleted_at' => $resource->deleted_at,
        ];
    }
}
