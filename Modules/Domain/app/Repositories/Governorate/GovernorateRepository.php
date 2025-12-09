<?php

declare(strict_types=1);

namespace Modules\Domain\Repositories\Governorate;

use App\Repositories\BaseRepository\BaseRepository;
use Modules\Domain\Models\Governorate;

final readonly class GovernorateRepository extends BaseRepository implements GovernorateRepositoryInterface
{
    public function __construct(Governorate $model)
    {
        parent::__construct($model);
    }
}
