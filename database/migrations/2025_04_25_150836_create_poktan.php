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
        Schema::create('poktan', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('phone_number')->unique();
            $table->string('address')->unique();
            $table->text('ktp')->unique();
            $table->unsignedBigInteger('desa_id');
            $table->unsignedFloat('luas_lahan', 8, 4);
            $table->enum('status', ['diverifikasi', 'ditolak'])->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('desa_id')->references('id')->on('desa');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poktan');
    }
};
