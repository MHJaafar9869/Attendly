<?php

declare(strict_types=1);

namespace Modules\Core\Repositories\Payment;

use Modules\Core\Repositories\Payment\PaymentRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use Modules\Core\Models\Payment;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }
}
