<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tuition_requests', function (Blueprint $table) {
            // PK
            $table->id('TutionID');

            // FK → learners.LearnerID
            $table->foreignId('LearnerID')
                  ->constrained('learners', 'LearnerID')
                  ->onDelete('cascade');

            $table->string('class');           // class/grade level
            $table->text('subjects');
            $table->integer('asked_salary');
            $table->string('curriculum');
            $table->string('days')->nullable();
            $table->string('location')->nullable();

            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tuition_requests');
    }
};
