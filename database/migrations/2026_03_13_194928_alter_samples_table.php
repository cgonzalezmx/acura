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
        Schema::table('samples', function(Blueprint $table) {
            $table->string('matrix')->nullable();
            $table->string('client')->nullable();
            $table->boolean('is_urgent')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('samples', function(Blueprint $table) {
            $table->dropColumn(['matrix', 'client', 'is_urgent']);
        });
    }
};
