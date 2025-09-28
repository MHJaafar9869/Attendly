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
        Schema::create('images', function (Blueprint $table) {
            $table->id();

            // Morph columns in case of ulids
            $table->string('imageable_id', 26);
            $table->string('imageable_type');

            $table->string('disk', 50)->default('public');
            $table->string('image_path', 255);
            $table->string('image_url', 255);
            $table->string('image_mime', 100);
            $table->string('image_alt')->nullable();

            $table->boolean('is_flagged')->default(false);
            $table->ulid('flagged_by')->nullable();
            $table->timestamp('flagged_at')->nullable();

            $table->foreign('flagged_by')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['imageable_id', 'imageable_type']);
            $table->index('is_flagged');
            $table->index('deleted_at');
            $table->index('disk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
