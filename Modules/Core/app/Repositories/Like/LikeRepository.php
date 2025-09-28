<?php

namespace Modules\Core\Repositories\Like;

use Modules\Core\Repositories\Like\LikeRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use Modules\Core\Models\Like;

class LikeRepository extends BaseRepository implements LikeRepositoryInterface
{
    public function __construct(Like $model)
    {
        parent::__construct($model);
    }
}
