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
        Schema::create('analysis_threshold', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analysis_id')->constrained();
            $table->foreignId('threshold_id')->constrained('report_thresholds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analysis_threshold');
    }
};
