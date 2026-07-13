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
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->unsignedTinyInteger('index')->default(1);
            $table->string('result')->nullable();
            $table->string('reported_result')->nullable();
            $table->string('measurement_units')->nullable();
            $table->string('minimal_quantification')->nullable();
            $table->string('method')->nullable();
            $table->string('uncertainty')->nullable();
            $table->boolean('registered')->default(false);
            $table->boolean('authorized')->default(false);
            $table->boolean('canceled')->default(false);
            $table->enum('veredict', ['compliant', 'non-compliant', 'n/a'])->nullable();
            $table->foreignId('authorized_by')->nullable()->constrained('users');
            $table->foreignId('reported_by')->nullable()->constrained('users');
            $table->foreignId('parameter_id')->constrained();
            $table->foreignId('sample_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
