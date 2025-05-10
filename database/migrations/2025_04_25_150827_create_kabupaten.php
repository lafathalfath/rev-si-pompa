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
        Schema::create('kabupaten', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('pj_id')->nullable();
            $table->unsignedBigInteger('provinsi_id');
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();

            $table->foreign('pj_id')->references('id')->on('users');
            $table->foreign('provinsi_id')->references('id')->on('provinsi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kabupaten');
    }
};
