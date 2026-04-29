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
        Schema::create('firebase_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Push, Pull, Audit
            $table->string('status'); // running, success, failed
            $table->json('summary')->nullable(); // {created: 0, updated: 0, skipped: 0, errors: 0}
            $table->integer('items_count')->default(0);
            $table->string('performed_by')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firebase_sync_logs');
    }
};
