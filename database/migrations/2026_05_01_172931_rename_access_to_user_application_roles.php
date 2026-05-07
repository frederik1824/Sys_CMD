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
        Schema::rename('user_application_access', 'user_application_roles');
    }

    public function down(): void
    {
        Schema::rename('user_application_roles', 'user_application_access');
    }
};
