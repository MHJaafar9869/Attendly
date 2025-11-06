<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Models\Status;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stripe_payments', function (Blueprint $table) {
            $table->id();
            $table->ulid('buyer_id');
            $table->foreign('buyer_id')->references('id')->on('users')->cascadeOnDelete();

            $table->string('payable_id');
            $table->string('payable_type');
            $table->index(['payable_id', 'payable_type']);
            $table->string('stripe_payment_intent_id')->nullable()->unique();
            $table->string('stripe_transaction_id')->unique();

            $table->integer('amount_cents');
            $table->string('currency', 3)->default('USD');
            $table->json('product_data')->nullable();
            $table->foreignIdFor(Status::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stripe_payments');
    }
};
