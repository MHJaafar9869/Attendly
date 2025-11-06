<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Models\Status;
use Modules\Core\Models\Type;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->string('teacher_code')->unique();
            
            $table->foreignIdFor(Type::class, 'teacher_type_id')
                ->nullable()
                ->constrained('types')
                ->nullOnDelete();

            $table->foreignIdFor(Status::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignUlid('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

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
        Schema::dropIfExists('teachers');
    }
};
