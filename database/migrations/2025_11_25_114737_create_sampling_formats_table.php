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
        Schema::create('sampling_formats', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->blamable();
            $table->string('identifier');
            $table->string('path')->nullable();
            $table->unsignedInteger('sequence_index')->default(1);
            $table->string('year');
            $table->foreignId('quote_id')->nullable()->constrained();
            $table->foreignId('entry_id')->nullable()->constrained('quote_entries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sampling_formats');
    }
};
