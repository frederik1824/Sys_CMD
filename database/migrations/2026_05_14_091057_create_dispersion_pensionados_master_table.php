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
        Schema::create('dispersion_pensionados_master', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('cedula')->unique()->index();
            $table->string('nss', 20)->nullable()->index();
            $table->string('nombre_completo');
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('genero', ['M', 'F', 'O'])->nullable();
            $table->string('tipo_pension')->nullable();
            $table->string('institucion_pension')->nullable();
            $table->decimal('monto_pension', 15, 2)->default(0);
            $table->timestamp('ultimo_pago_confirmado_at')->nullable();
            $table->string('estado_sistema')->default('Activo'); // Activo, Suspendido, Fallecido
            $table->json('data_adicional')->nullable(); // Para cualquier otro dato extra
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispersion_pensionados_master');
    }
};
