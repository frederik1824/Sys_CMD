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
        Schema::create('meta_traspasos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agente_id')->constrained('agente_traspasos')->onDelete('cascade');
            $table->string('periodo'); // YYYY-MM
            $table->integer('meta_cantidad')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meta_traspasos');
    }
};
