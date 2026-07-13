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
        Schema::table('quote_selected_sampling_sites', function(Blueprint $table) {
            $table->renameColumn('is_main_saite', 'is_main_site');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_selected_sampling_sites', function(Blueprint $table) {
            $table->renameColumn('is_main_site', 'is_main_saite');
        });
    }
};
