<?php

declare(strict_types=1);

namespace Modules\Core\Repositories\PaymentGateway;

use App\Repositories\BaseRepository\BaseRepository;
use Exception;
use Modules\Core\Models\StripePayment;
use Stripe\Account;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\StripeClient;

class StripeRepository extends BaseRepository implements PaymentGatewayRepositoryInterface
{
    public function __construct(
        protected StripePayment $payment,
        protected StripeClient $stripe
    ) {
        parent::__construct($payment);
    }

    public function charge(string $customerId, string $ownerId, int $amount, array $productData): PaymentIntent
    {
        $payment = $this->create([
            'merchant_id' => $ownerId,
            'user_id' => auth()->id(),
            'amount' => $amount * 100,
            'currency' => 'usd',
            'product_data' => $productData,
        ]);

        $idempotencyKey = "payment_{$payment->id}";

        try {
            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => $amount * 100,
                'currency' => 'usd',
                'customer' => $customerId,
                'automatic_payment_methods' => ['enabled' => true],
            ], [
                'idempotency_key' => $idempotencyKey,
            ]);

            $this->update($payment->id, [
                'stripe_payment_intent_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
            ]);

            return $paymentIntent;

        } catch (ApiErrorException $e) {
            $this->update($payment->id, [
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function refund(string $transactionId, int $amount): Refund
    {
        $refund = $this->stripe->refunds->create([
            'payment_intent' => $transactionId,
            'amount' => $amount * 100,
        ]);
        $this->update($refund->id, ['status' => 'refunded']);

        return $refund;
    }

    public function balance(string $accountId): array
    {
        try {
            $balance = $this->stripe->balance->retrieve([
                'stripe_account' => $accountId,
            ]);

            return [
                'available' => $balance->available,
                'pending' => $balance->pending,
            ];
        } catch (ApiErrorException $e) {
            throw new Exception('Failed to retrieve balance: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function createAccount(array $options = []): Account
    {
        try {
            $account = $this->stripe->accounts->create(array_merge([
                'type' => 'express',
                'country' => 'US',
                'email' => $options['email'] ?? null,
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
            ], $options));

            $balance = $this->balance($account->id);

            // Save to your database
            // Assuming you have a merchant/account repository
            // $this->accounts->create([
            //     'stripe_account_id' => $account->id,
            //     'email' => $account->email,
            //     'type' => $account->type,
            //     'status' => $account->charges_enabled ? 'active' : 'pending',
            // ]);

            return $account;

        } catch (ApiErrorException $e) {
            throw new Exception('Failed to create account: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
