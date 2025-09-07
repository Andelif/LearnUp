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
        Schema::create('users', function (Blueprint $table) {
            // Creates a big integer auto-increment PK.
            // MySQL => UNSIGNED BIGINT; Postgres => BIGSERIAL. Portable.
            $table->id();

            $table->string('name');
            $table->string('email')->unique();

            // Use timezone-aware timestamps on Postgres
            $table->timestampTz('email_verified_at')->nullable();

            $table->string('password');
            $table->rememberToken();

            // Enum becomes a CHECK constraint on Postgres via Schema builder
            $table->enum('role', ['learner', 'tutor', 'admin'])->default('learner');

            // Let Laravel manage timestamps in app code (no ON UPDATE clause needed)
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
