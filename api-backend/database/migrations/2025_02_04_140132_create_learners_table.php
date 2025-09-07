<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learners', function (Blueprint $table) {
            // Auto-increment PK: BIGSERIAL in Postgres, UNSIGNED BIGINT in MySQL
            $table->id('LearnerID');

            // FK to users table
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('full_name')->nullable();
            $table->string('guardian_full_name')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('guardian_contact_number')->nullable();

            // ENUM in Postgres is messy; use check constraint via Laravel enum()
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();

            $table->text('address')->nullable();

            // Use Laravel's timestamps with timezone
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learners');
    }
};
