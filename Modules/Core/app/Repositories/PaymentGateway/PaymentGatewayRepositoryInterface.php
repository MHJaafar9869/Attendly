<?php

declare(strict_types=1);

namespace Modules\Core\Repositories\PaymentGateway;

interface PaymentGatewayRepositoryInterface
{
    /**
     * @return \Stripe\Charge|array
     */
    public function charge(string $customerId, string $ownerId, int $amount, array $productData);

    public function refund(string $transactionId, int $amount);

    public function balance(string $ownerId);

    public function createAccount(array $options = []);
}
