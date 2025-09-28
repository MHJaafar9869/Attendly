<?php

namespace Modules\Core\Repositories\Type;

use Modules\Core\Repositories\Type\TypeRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use Modules\Core\Models\Type;

class TypeRepository extends BaseRepository implements TypeRepositoryInterface
{
    public function __construct(Type $model)
    {
        parent::__construct($model);
    }
}
