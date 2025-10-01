<?php

namespace Modules\Core\database\seeders\PayPalPayment;

use Illuminate\Database\Seeder;
use Modules\Core\Models\PayPalPayment;

class PayPalPaymentSeeder extends Seeder
{
    public function run(): void
    {
        PayPalPayment::firstOrCreate([
                'user_id' => '1',
                'payable_id' => '1',
                'payable_type' => 'Sample payable_type 1',
                'paypal_transaction_id' => '1',
                'amount_cents' => 'Sample amount_cents 1',
                'currency' => 'Sample currency 1',
                'product_data' => json_encode(['sample' => 'Sample product_data 1']),
                'status_id' => '1',
        ]);

        PayPalPayment::firstOrCreate([
                'user_id' => '2',
                'payable_id' => '2',
                'payable_type' => 'Sample payable_type 2',
                'paypal_transaction_id' => '2',
                'amount_cents' => 'Sample amount_cents 2',
                'currency' => 'Sample currency 2',
                'product_data' => json_encode(['sample' => 'Sample product_data 2']),
                'status_id' => '2',
        ]);

        PayPalPayment::firstOrCreate([
                'user_id' => '3',
                'payable_id' => '3',
                'payable_type' => 'Sample payable_type 3',
                'paypal_transaction_id' => '3',
                'amount_cents' => 'Sample amount_cents 3',
                'currency' => 'Sample currency 3',
                'product_data' => json_encode(['sample' => 'Sample product_data 3']),
                'status_id' => '3',
        ]);

        PayPalPayment::firstOrCreate([
                'user_id' => '4',
                'payable_id' => '4',
                'payable_type' => 'Sample payable_type 4',
                'paypal_transaction_id' => '4',
                'amount_cents' => 'Sample amount_cents 4',
                'currency' => 'Sample currency 4',
                'product_data' => json_encode(['sample' => 'Sample product_data 4']),
                'status_id' => '4',
        ]);

        PayPalPayment::firstOrCreate([
                'user_id' => '5',
                'payable_id' => '5',
                'payable_type' => 'Sample payable_type 5',
                'paypal_transaction_id' => '5',
                'amount_cents' => 'Sample amount_cents 5',
                'currency' => 'Sample currency 5',
                'product_data' => json_encode(['sample' => 'Sample product_data 5']),
                'status_id' => '5',
        ]);

    }
}
