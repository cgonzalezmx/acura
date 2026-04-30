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
        Schema::table('report_thresholds', function (Blueprint $table) {
            $table->unique(['report_id', 'parameter_id'], 'unique_threshold');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_thresholds', function (Blueprint $table) {
            $table->dropUnique('unique_threshold');
        });
    }
};
