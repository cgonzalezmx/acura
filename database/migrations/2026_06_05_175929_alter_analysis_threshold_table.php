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
        Schema::table('analysis_threshold', function(Blueprint $table) {
            $table->dropForeign(['threshold_id']);
            $table->dropColumn(['threshold_id']);
            $table->foreignId('threshold_id')->constrained('sample_thresholds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analysis_threshold', function(Blueprint $table) {});
    }
};
