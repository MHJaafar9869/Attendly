<?php

namespace Modules\Core\Repositories\Role;

use App\Repositories\BaseRepository\BaseRepository;
use Modules\Core\Models\Role;

final readonly class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }
}
