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
        Schema::create('backup_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_automated')->default(false);
            $table->string('schedule_frequency')->default('daily'); // daily, weekly, monthly
            $table->string('schedule_time')->default('02:00'); // Time of day
            $table->integer('max_backups')->default(5); // Retain max 5 backups
            $table->string('custom_path')->nullable(); // Optional external/custom path
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_settings');
    }
};
