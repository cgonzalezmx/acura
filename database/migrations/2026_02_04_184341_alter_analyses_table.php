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
        Schema::table('analyses', function (Blueprint $table) {
            $table->unsignedTinyInteger('registration_counter')->default(0);
            $table->string('log')->nullable();
            $table->timestamp('authorized_at')->nullable();
            $table->timestamp('analyzed_at')->nullable();
            $table->timestamp('saved_at')->nullable();
            $table->enum('range', ['low', 'mid', 'high'])->default('mid');
            $table->unsignedInteger('lab_matrix_id')->nullable();
            $table->foreignId('take_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->dropColumn([
                'registration_counter',
                'log',
                'authorized_at',
                'saved_at',
                'range',
                'take_id'
            ]);
        });
    }
};
