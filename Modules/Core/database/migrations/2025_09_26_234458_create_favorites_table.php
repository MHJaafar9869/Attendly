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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->string('favoriteable_id', 26);
            $table->string('favoriteable_type');
            $table->string('collection')->default('My Favorites');

            $table->ulid('user_id');
            $table->unsignedBigInteger('favorites_count')->default(0);

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['favoriteable_id', 'favoriteable_type']);
            $table->unique(['user_id', 'favoriteable_id', 'favoriteable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
