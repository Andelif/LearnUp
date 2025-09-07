<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            // PK
            $table->id('ApplicationID');

            // FKs (note the custom PK names on related tables)
            $table->foreignId('tution_id')     // keeping your current column name
                  ->constrained('tuition_requests', 'TutionID')
                  ->onDelete('cascade');

            $table->foreignId('learner_id')
                  ->constrained('learners', 'LearnerID')
                  ->onDelete('cascade');

            $table->foreignId('tutor_id')
                  ->constrained('tutors', 'TutorID')
                  ->onDelete('cascade');

            $table->boolean('matched')->default(false);

            // Enums → CHECK constraints on Postgres via Schema builder
            $table->enum('status', ['Applied', 'Shortlisted', 'Confirmed', 'Cancelled'])->nullable();
            $table->enum('payment_status', ['Pending', 'Completed'])->default('Pending');

            // TZ-aware timestamps
            $table->timestampsTz();

            // (Optional) helpful indexes
            // $table->index(['tution_id']);
            // $table->index(['learner_id']);
            // $table->index(['tutor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
