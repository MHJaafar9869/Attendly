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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('action_type_id')->constrained('types')->cascadeOnDelete();
            $table->morphs('loggable');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['action_type_id', 'loggable_type'], 'activity_logs_action_loggable_index');
            $table->index(['user_id', 'action_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actions');
    }
};
