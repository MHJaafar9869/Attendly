<?php

declare(strict_types=1);

namespace Modules\Domain\Repositories\PaymentGateway;

use App\Repositories\BaseRepository\BaseRepository;
use Modules\Domain\Models\PayPalPayment;

final readonly class PayPalRepository extends BaseRepository implements PaymentGatewayRepositoryInterface
{
    public function __construct(
        protected PayPalPayment $payPal
    ) {
        parent::__construct($payPal);
    }

    public function charge(string $customerId, string $ownerId, int $amount, array $productData)
    {
        // ...
    }

    public function refund(string $transactionId, int $amount)
    {
        // ...
    }

    public function balance(string $ownerId)
    {
        // ...
    }

    public function createAccount(array $options = [])
    {
        // ...
    }
}
