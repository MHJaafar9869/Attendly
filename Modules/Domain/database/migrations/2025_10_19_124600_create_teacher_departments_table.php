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
            $table->timestamps();
            $table->unique(['department_id', 'teacher_id']);
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
