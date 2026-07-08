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
        Schema::table('sample_thresholds', function(Blueprint $table) {
            $table->integer('max_numeric_value')->nullable();
            $table->integer('min_numeric_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sample_thresholds', function(Blueprint $table) {
            $table->dropColumn([
                'max_numeric_value',
                'min_numeric_value'
            ]);
        });
    }
};
