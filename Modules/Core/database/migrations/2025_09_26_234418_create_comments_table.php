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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            // Morph columns in case of ulids
            $table->string('commentable_id', 26);
            $table->string('commentable_type');

            $table->text('content');
            $table->ulid('user_id');

            $table->boolean('is_flagged')->default(false);
            $table->ulid('flagged_by')->nullable();
            $table->timestamp('flagged_at')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('flagged_by')->references('id')->on('users')->onDelete('set null');
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['commentable_id', 'commentable_type']);
            $table->index('user_id');
            $table->index('deleted_at');
            $table->index('parent_id');
            $table->index('is_flagged');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
