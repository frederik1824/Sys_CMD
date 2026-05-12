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
        Schema::create('cloud_sync_checkpoints', function (Blueprint $table) {
            $table->id();
            $table->string('process_name');
            $table->string('company')->nullable();
            $table->timestamp('last_success_at')->nullable();
            $table->string('last_document_id')->nullable();
            $table->timestamp('last_document_updated_at')->nullable();
            $table->integer('processed_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->integer('read_count')->default(0);
            $table->integer('batch_size')->default(500);
            $table->string('status')->default('idle'); 
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->string('sync_type')->default('incremental');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloud_sync_checkpoints');
    }
};
