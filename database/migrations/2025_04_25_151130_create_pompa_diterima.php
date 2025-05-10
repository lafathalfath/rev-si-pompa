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
        Schema::create('pompa_diterima', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pompa_usulan_id');
            $table->unsignedInteger('total_unit');
            $table->enum('status', ['diverifikasi', 'ditolak'])->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('pompa_usulan_id')->references('id')->on('pompa_usulan');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pompa_diterima');
    }
};
