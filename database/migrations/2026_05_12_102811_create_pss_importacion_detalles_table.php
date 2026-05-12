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
        Schema::create('pss_importacion_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('importacion_id')->constrained('pss_importaciones')->onDelete('cascade');
            $table->integer('fila');
            $table->string('estado'); // success, error, skipped, duplicate
            $table->json('datos_originales')->nullable();
            $table->text('error_mensaje')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pss_importacion_detalles');
    }
};
