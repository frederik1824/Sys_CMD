<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabla de Aplicaciones (Centralizada)
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // ej: 'afiliacion', 'cmd', 'logistica'
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('route')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->default('blue');
            $table->enum('status', ['active', 'development', 'maintenance'])->default('active');
            $table->boolean('is_visible')->default(true);
            $table->integer('order_weight')->default(0);
            $table->timestamps();
        });

        // 2. Relación Usuario -> Aplicación -> Rol
        // Esto permite que Spatie trabaje de forma contextual
        Schema::create('user_application_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamp('assigned_at')->useCurrent();
            $table->foreignId('assigned_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['user_id', 'application_id']); // Un usuario tiene un acceso principal por app
        });

        // 3. Logs de Auditoría de Seguridad
        Schema::create('access_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performer_id')->constrained('users'); // Quién hizo el cambio
            $table->foreignId('target_user_id')->constrained('users'); // A quién se le cambió
            $table->string('action'); // 'grant', 'revoke', 'change_role'
            $table->string('application_key')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_audit_logs');
        Schema::dropIfExists('user_application_access');
        Schema::dropIfExists('applications');
    }
};
