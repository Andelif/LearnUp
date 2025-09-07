<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutors', function (Blueprint $table) {
            // PK (BIGSERIAL on Postgres)
            $table->id('TutorID');

            // FK → users.id
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('full_name')->nullable();
            $table->string('address')->nullable();
            $table->string('contact_number')->nullable();

            // Use enum → becomes CHECK on Postgres
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();

            $table->integer('preferred_salary')->nullable();
            $table->string('qualification')->nullable();
            $table->string('experience')->nullable();
            $table->string('currently_studying_in')->nullable();
            $table->string('preferred_location')->nullable();
            $table->string('preferred_time')->nullable();

            $table->boolean('availability')->default(true);

            // TZ-aware timestamps; no ON UPDATE needed
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutors');
    }
};
