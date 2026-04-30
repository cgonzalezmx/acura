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
        Schema::table('parameters', function(Blueprint $table) {
            $table->dropForeign(['parameter_category_id']);
            $table->dropColumn('parameter_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parameters', function(Blueprint $table) {
            $table->foreignId('parameter_category_id')->constrained();
        });
    }
};
