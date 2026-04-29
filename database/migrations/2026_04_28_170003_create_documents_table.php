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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('departamento_id')->constrained('departamentos');
            $table->foreignId('document_type_id')->constrained();
            $table->foreignId('document_status_id')->constrained();
            $table->boolean('is_regulatory')->default(false);
            $table->enum('visibility', ['public', 'department', 'private'])->default('public');
            $table->foreignId('created_by')->constrained('users');
            $table->date('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
