<?php

namespace Modules\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\Status;
use Modules\Core\Models\User;

// use Modules\Domain\Database\Factories\PaymentFactory;

class StripePayment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id', 'payable_id', 'payable_type',
        'stripe_payment_intent_id', 'amount_cents',
        'currency', 'product_data', 'status_id',
    ];

    // protected static function newFactory(): PaymentFactory
    // {
    //     // return PaymentFactory::new();
    // }

    /*
    |--------------------------------------------------------------------------
    |  Relations
    |--------------------------------------------------------------------------
    |
    */

    public function payable()
    {
        return $this->morphTo();
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
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
