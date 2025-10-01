<?php

namespace Modules\Core\database\seeders\Payment;

use Illuminate\Database\Seeder;
use Modules\Core\Models\StripePayment;

class StripePaymentSeeder extends Seeder
{
    public function run(): void
    {
        StripePayment::firstOrCreate([
            'owner_id' => '1',
            'user_id' => '01k67f1k3qg9rzcxd7z5pvq7c7',
            'stripe_payment_intent_id' => '1',
            'amount' => 'Sample amount 1',
            'currency' => 'Sample currency 1',
            'product_data' => json_encode(['sample' => 'Sample product_data 1']),
            'status' => 'Sample status 1',
        ]);

        StripePayment::firstOrCreate([
            'owner_id' => '2',
            'user_id' => '01k67f1k3qg9rzcxd7z5pvq7c7',
            'stripe_payment_intent_id' => '2',
            'amount' => 'Sample amount 2',
            'currency' => 'Sample currency 2',
            'product_data' => json_encode(['sample' => 'Sample product_data 2']),
            'status' => 'Sample status 2',
        ]);

        StripePayment::firstOrCreate([
            'owner_id' => '3',
            'user_id' => '01k67f1k3qg9rzcxd7z5pvq7c7',
            'stripe_payment_intent_id' => '3',
            'amount' => 'Sample amount 3',
            'currency' => 'Sample currency 3',
            'product_data' => json_encode(['sample' => 'Sample product_data 3']),
            'status' => 'Sample status 3',
        ]);

        StripePayment::firstOrCreate([
            'owner_id' => '4',
            'user_id' => '01k67f1k3qg9rzcxd7z5pvq7c7',
            'stripe_payment_intent_id' => '4',
            'amount' => 'Sample amount 4',
            'currency' => 'Sample currency 4',
            'product_data' => json_encode(['sample' => 'Sample product_data 4']),
            'status' => 'Sample status 4',
        ]);

        StripePayment::firstOrCreate([
            'owner_id' => '5',
            'user_id' => '01k67f1k3qg9rzcxd7z5pvq7c7',
            'stripe_payment_intent_id' => '5',
            'amount' => 'Sample amount 5',
            'currency' => 'Sample currency 5',
            'product_data' => json_encode(['sample' => 'Sample product_data 5']),
            'status' => 'Sample status 5',
        ]);

    }
}
