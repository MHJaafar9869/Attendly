<?php

namespace Modules\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\Status;
use Modules\Core\Models\User;

// use Modules\Domain\Database\Factories\PayPalPaymentFactory;

class PayPalPayment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'buyer_id',
        'payable_id',
        'payable_type',
        'paypal_transaction_id',
        'transaction_id',
        'amount_cents',
        'currency',
        'product_data',
        'status_id',
    ];

    // protected static function newFactory(): PayPalPaymentFactory
    // {
    //     // return PayPalPaymentFactory::new();
    // }

    /*
    |--------------------------------------------------------------------------
    |  Relations
    |--------------------------------------------------------------------------
    |
    */

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function payable()
    {
        return $this->morphTo();
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    protected function casts(): array
    {
        return [
            'product_data' => 'array',
        ];
    }
}
