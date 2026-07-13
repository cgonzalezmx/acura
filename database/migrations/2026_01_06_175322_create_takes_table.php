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
        Schema::create('takes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->blamable();
            $table->timestamp('timestamp');
            $table->string('color');
            $table->string('odour');
            $table->string('appearance');
            $table->unsignedTinyInteger('sequence');
            $table->foreignId('sample_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('takes');
    }
};
