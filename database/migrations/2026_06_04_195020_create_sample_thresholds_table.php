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
        Schema::create('sample_thresholds', function (Blueprint $table) {
            $table->id();
            $table->string('max');
            $table->string('min')->nullable();
            $table->boolean('passed')->nullable();
            $table->boolean('enabled')->default(true);
            $table->char('letter', 1);
            $table->foreignId('parameter_id')->constrained();
            $table->foreignId('sample_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sample_thresholds');
    }
};
