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
        Schema::table('quote_entry_reports', function (Blueprint $table) {
            $table->unique('report_id', 'unique_report');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_entry_reports', function (Blueprint $table) {
            //
            $table->dropUnique('unique_report');
        });
    }
};
