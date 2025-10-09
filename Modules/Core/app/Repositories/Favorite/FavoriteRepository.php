<?php

declare(strict_types=1);

namespace Modules\Core\Repositories\Favorite;

use App\Repositories\BaseRepository\BaseRepository;
use Modules\Core\Models\Favorite;

class FavoriteRepository extends BaseRepository implements FavoriteRepositoryInterface
{
    public function __construct(Favorite $model)
    {
        parent::__construct($model);
    }
}
