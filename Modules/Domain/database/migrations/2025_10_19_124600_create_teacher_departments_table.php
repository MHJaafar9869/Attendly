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
        Schema::create('teacher_departments', function (Blueprint $table) {
            $table->id();

            $table->foreignUlid('department_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('teacher_id')->constrained()->cascadeOnDelete();

            $table->foreignUlid('assigned_by')->constrained('users')->cascadeOnDelete();

            $table->string('role')->nullable();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('unassigned_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['department_id', 'teacher_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_departments');
    }
};
