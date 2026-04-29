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
        Schema::create('evidencia_afiliados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('afiliado_id')->constrained('afiliados')->onDelete('cascade');
            $table->enum('tipo_documento', ['acuse_recibo', 'formulario_firmado', 'otro']);
            $table->enum('status', ['pendiente', 'recibido', 'validado'])->default('pendiente');
            $table->string('file_path')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users'); // quien subió
            $table->foreignId('validated_by')->nullable()->constrained('users'); // quien validó
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidencia_afiliados');
    }
};
