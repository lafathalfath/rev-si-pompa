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
        Schema::create('pompa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('desa_id');
            $table->unsignedBigInteger('poktan_id');
            $table->unsignedFloat('luas_lahan', 8, 4);
            $table->unsignedFloat('total_tanam', 8, 4)->default(0);
            $table->unsignedInteger('diusulkan_unit');
            $table->unsignedInteger('diterima_unit')->default(0);
            $table->unsignedInteger('dimanfaatkan_unit')->default(0);
            $table->unsignedBigInteger('status_id')->default(1);
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('desa_id')->references('id')->on('desa');
            $table->foreign('poktan_id')->references('id')->on('poktan');
            $table->foreign('status_id')->references('id')->on('status');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pompa');
    }
};
