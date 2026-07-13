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
        Schema::table('quote_client_records', function(Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('quote_entries', function(Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('quote_entry_parameters', function(Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('quote_entry_reports', function(Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('report_thresholds', function(Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
