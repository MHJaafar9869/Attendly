<?php

declare(strict_types=1);

namespace Modules\Domain\Services;

use InvalidArgumentException;
use Modules\Domain\Repositories\PaymentGateway\PaymentGatewayRepositoryInterface;
use Modules\Domain\Repositories\PaymentGateway\PayPalRepository;
use Modules\Domain\Repositories\PaymentGateway\StripeRepository;

class PaymentGatewayService
{
    public function __construct(
        protected StripeRepository $stripe,
        protected PayPalRepository $paypal
    ) {}

    public function resolve(string $gateway): PaymentGatewayRepositoryInterface
    {
        return match ($gateway) {
            'stripe' => $this->stripe,
            'paypal' => $this->paypal,
            default => throw new InvalidArgumentException("Unsupported payment gateway: {$gateway}"),
        };
    }
}
