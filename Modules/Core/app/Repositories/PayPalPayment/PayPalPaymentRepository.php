<?php

declare(strict_types=1);

namespace Modules\Core\Repositories\PayPalPayment;

use App\Repositories\BaseRepository\BaseRepository;
use Modules\Core\Models\PayPalPayment;

class PayPalPaymentRepository extends BaseRepository implements PayPalPaymentRepositoryInterface
{
    public function __construct(PayPalPayment $model)
    {
        parent::__construct($model);
    }
}
