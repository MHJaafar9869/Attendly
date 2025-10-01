<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('owner_id');
            $table->ulid('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('stripe_payment_intent_id')->nullable()->unique();
            $table->integer('amount'); // in Dollar
            $table->string('currency', 3)->default('usd');
            $table->json('product_data')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
