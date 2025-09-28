<?php

namespace Modules\Core\Repositories\Permission;

use Modules\Core\Repositories\Permission\PermissionRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use Modules\Core\Models\Permission;

class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface
{
    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }
}
