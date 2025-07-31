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
        Schema::create('pemanfaatan_pompa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pompa_id');
            $table->unsignedInteger('total_unit');
            $table->unsignedFloat('luas_tanam', 8, 4);
            $table->unsignedBigInteger('bukti_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('pompa_id')->references('id')->on('pompa')->onDelete('cascade');
            $table->foreign('bukti_id')->references('id')->on('document');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemanfaatan_pompa');
    }
};
