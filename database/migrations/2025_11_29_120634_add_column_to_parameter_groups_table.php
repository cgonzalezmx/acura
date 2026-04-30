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
        Schema::table('parameter_groups', function (Blueprint $table) {
            $table->unsignedInteger('order')->change()->default(1);
            $table->string('required_sample_volume');
            $table->string('remarks');
            $table->foreignId('sample_container_id')->constrained();
            $table->foreignId('sample_preserver_id')->constrained();
            $table->foreignId('label_color_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parameter_groups', function (Blueprint $table) {
            $table->dropForeign(['sample_container_id']);
            $table->dropForeign(['sample_preserver_id']);
            $table->dropColumn([
                'required_sample_volume',
                'remarks',
                'sample_container_id',
                'sample_preserver_id'
            ]);
        });
    }
};