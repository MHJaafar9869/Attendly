<?php

namespace Modules\Core\Repositories\Comment;

use App\Repositories\BaseRepository\BaseRepository;
use Modules\Core\Models\Comment;

class CommentRepository extends BaseRepository implements CommentRepositoryInterface
{
    public function __construct(Comment $model)
    {
        parent::__construct($model);
    }
}
