<?php

namespace Modules\Core\Repositories\Status;

use App\Repositories\BaseRepository\BaseRepository;
use Modules\Core\Models\Status;

class StatusRepository extends BaseRepository implements StatusRepositoryInterface
{
    public function __construct(Status $model)
    {
        parent::__construct($model);
    }
}
