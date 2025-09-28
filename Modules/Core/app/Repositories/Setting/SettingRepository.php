<?php

namespace Modules\Core\Repositories\Setting;

use Modules\Core\Repositories\Setting\SettingRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use Modules\Core\Models\Setting;

class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    public function __construct(Setting $model)
    {
        parent::__construct($model);
    }
}
