<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfirmedTuitionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('confirmed_tuitions', function (Blueprint $table) {
            // PK
            $table->id('ConfirmedTuitionID');

            // FKs
            $table->foreignId('application_id')
                  ->constrained('applications', 'ApplicationID')
                  ->onDelete('cascade');

            $table->foreignId('tution_id') // keeping your existing column name
                  ->constrained('tuition_requests', 'TutionID')
                  ->onDelete('cascade');

            $table->decimal('FinalizedSalary', 10, 2);
            $table->string('FinalizedDays');

            // Enum -> CHECK constraint in Postgres
            $table->enum('Status', ['Ongoing', 'Ended'])->default('Ongoing');

            // Default now(), timezone-aware
            $table->timestampTz('ConfirmedDate')->useCurrent();

            // (Optional) also track created_at/updated_at:
            // $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('confirmed_tuitions');
    }
}
