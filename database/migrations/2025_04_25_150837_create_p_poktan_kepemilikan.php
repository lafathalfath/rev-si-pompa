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
        Schema::create('p_poktan_kepemilikan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('poktan_id');
            $table->unsignedBigInteger('document_id');
            $table->timestamps();

            $table->foreign('poktan_id')->references('id')->on('poktan');
            $table->foreign('document_id')->references('id')->on('document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_poktan_kepemilikan');
    }
};
