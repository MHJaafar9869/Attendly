<?php

namespace Modules\Core\Repositories\Image;

use App\Repositories\BaseRepository\BaseRepository;
use Modules\Core\Models\Image;

final readonly class ImageRepository extends BaseRepository implements ImageRepositoryInterface
{
    public function __construct(Image $model)
    {
        parent::__construct($model);
    }
}
