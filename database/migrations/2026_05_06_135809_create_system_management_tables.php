<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Registro de Versiones y Actualizaciones
        Schema::create('system_updates', function (Blueprint $table) {
            $table->id();
            $table->string('version')->unique(); // Ej: 1.2.0
            $table->integer('build_number')->unique(); // Ej: 2026050601
            $table->string('type')->default('patch'); // core, module, hotfix, patch
            $table->text('changelog')->nullable();
            $table->string('status')->default('pending'); // pending, processing, success, failed, rolled_back
            $table->string('package_path')->nullable();
            $table->string('checksum')->nullable();
            
            // Detalles de ejecución
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error_log')->nullable();
            
            $table->unsignedBigInteger('executed_by')->nullable();
            $table->timestamps();
        });

        // Registro de Backups (Automáticos y Manuales)
        Schema::create('system_backups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('update_id')->nullable(); // Vinculado a un update si fue automático
            $table->string('filename');
            $table->string('path');
            $table->string('type')->default('full'); // full, database, storage
            $table->bigInteger('size_bytes')->default(0);
            $table->string('status')->default('ready'); // ready, corrupted, deleted
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_backups');
        Schema::dropIfExists('system_updates');
    }
};
