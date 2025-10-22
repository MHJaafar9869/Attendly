<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Domain\Models\Governorate;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('student_code')->unique();
            $table->string('hashed_national_id')->unique();
            $table->enum('gender', ['male', 'female']);
            $table->string('academic_year');
            $table->string('section');

            $table->string('phone')->nullable()->unique();
            $table->string('secondary_phone')->nullable()->unique();

            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->foreignIdFor(Governorate::class)->nullable()->constrained();

            $table->timestamps();
            $table->softDeletes();

            $table->index('created_at');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
