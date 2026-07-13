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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->blamable();
            $table->softDeletes();
            $table->string('name');
            $table->string('parameter');
            $table->boolean('authorized')->default(false);
            $table->timestamp('analyzed_at')->nullable();
            $table->timestamp('saved_at')->nullable();
            $table->timestamp('authorized_at')->nullable();
            $table->time('checkin_time')->nullable();
            $table->time('checkout_time')->nullable();
            $table->string('log')->nullable();
            $table->string('solutions_log')->nullable();
            $table->enum('range', ['low', 'mid', 'high'])->default('mid');
            $table->string('matrix');
            $table->string('minimal_quantification')->nullable();
            $table->foreignId('sample_storage_id')->nullable()->constrained();
            $table->foreignId('authorized_by')->nullable()->constrained('users');
            $table->foreignId('analysis_area_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
