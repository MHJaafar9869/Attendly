<?php

declare(strict_types=1);

namespace Modules\Domain\Repositories\PaymentGateway;

use App\Repositories\BaseRepository\BaseRepositoryInterface;

interface PaymentGatewayRepositoryInterface extends BaseRepositoryInterface
{
    public function charge(string $customerId, string $ownerId, int $amount, array $productData);

    public function refund(string $transactionId, int $amount);

    public function balance(string $ownerId);

    public function createAccount(array $options = []);
}
