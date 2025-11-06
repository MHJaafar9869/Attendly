<?php

namespace Modules\Core\Repositories\Permission;

use App\Repositories\BaseRepository\BaseRepository;
use Modules\Core\Models\Permission;

final readonly class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface
{
    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }
}
