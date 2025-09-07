<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            // PK
            $table->id('AdminID');

            // FK (unique, one admin per user)
            $table->foreignId('user_id')
                  ->unique()
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('full_name');
            $table->string('address')->nullable();
            $table->string('contact_number', 20)->nullable();

            $table->boolean('permission_req')->default(false);
            $table->integer('match_made')->default(0);
            $table->string('task_assigned')->nullable();

            // Laravel-managed, timezone-aware
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
