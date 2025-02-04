<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id('ApplicationID');
            $table->unsignedBigInteger('tution_id'); // Foreign key to tuition_requests table
            $table->unsignedBigInteger('learner_id'); // Foreign key to learners table
            $table->unsignedBigInteger('tutor_id'); // Foreign key to tutors table
            $table->timestamps();
    
            $table->foreign('tution_id')->references('TutionID')->on('tuition_requests')->onDelete('cascade');
            $table->foreign('learner_id')->references('LearnerID')->on('learners')->onDelete('cascade');
            $table->foreign('tutor_id')->references('TutorID')->on('tutors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applications');
    }
};
