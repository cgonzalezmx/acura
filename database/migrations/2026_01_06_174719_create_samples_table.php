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
        Schema::create('samples', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->blamable();
            $table->string('sampling_point');
            $table->integer('sample_temperature');
            $table->unsignedInteger('total_containers');
            $table->string('refrigerator');
            $table->timestamp('reception_date');
            $table->boolean('canceled')->default(false);
            $table->enum('acceptance', ['ok', 'conditioned', 'not'])->nullable();
            $table->text('observation')->nullable();
            $table->string('identifier')->nullable();
            $table->foreignId('sampling_format_id')->constrained();
            $table->foreignId('sampled_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('samples');
    }
};
