<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Domain\Models\Major;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('academic_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('year_number');
            $table->string('group_code', 10);
            $table->string('display_name');
            $table->foreignIdFor(Major::class)->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->unique(['year_number', 'group_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_levels');
    }
};
