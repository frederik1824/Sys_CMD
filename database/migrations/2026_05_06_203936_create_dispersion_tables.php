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
        // 1. Catálogo de Indicadores de Dispersión
        Schema::create('dispersion_indicators', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('category')->nullable(); // P.ej: Afiliados, Montos, Otros
            $table->boolean('is_total')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('order_weight')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Catálogo de Tipos de Bajas
        Schema::create('dispersion_baja_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->integer('order_weight')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Periodos Mensuales
        Schema::create('dispersion_periods', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->integer('month');
            $table->string('status')->default('pending'); // pending, closed, reopened
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('closed_by')->nullable()->constrained('users');
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['year', 'month']);
        });

        // 4. Cortes Específicos
        Schema::create('dispersion_cortes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('dispersion_periods')->onDelete('cascade');
            $table->integer('corte_number'); // 1 o 2
            $table->date('reception_date')->nullable();
            $table->string('status')->default('pending'); // pending, in_progress, validated, closed
            $table->foreignId('user_id')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['period_id', 'corte_number']);
        });

        // 5. Valores de Dispersión
        Schema::create('dispersion_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corte_id')->constrained('dispersion_cortes')->onDelete('cascade');
            $table->foreignId('indicator_id')->constrained('dispersion_indicators');
            $table->bigInteger('quantity')->default(0);
            $table->decimal('amount', 18, 2)->default(0);
            $table->timestamps();
        });

        // 6. Valores de Bajas
        Schema::create('dispersion_baja_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corte_id')->constrained('dispersion_cortes')->onDelete('cascade');
            $table->foreignId('baja_type_id')->constrained('dispersion_baja_types');
            $table->integer('quantity')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispersion_baja_values');
        Schema::dropIfExists('dispersion_values');
        Schema::dropIfExists('dispersion_cortes');
        Schema::dropIfExists('dispersion_periods');
        Schema::dropIfExists('dispersion_baja_types');
        Schema::dropIfExists('dispersion_indicators');
    }
};
