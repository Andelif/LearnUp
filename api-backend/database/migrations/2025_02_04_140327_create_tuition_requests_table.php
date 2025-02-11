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
        Schema::create('tuition_requests', function (Blueprint $table) {
            $table->id('TutionID');
            $table->unsignedBigInteger('learner_id'); // Foreign key to learners table
            $table->string('class');
            $table->text('subjects');
            $table->integer('asked_salary');
            $table->string('curriculum');
            $table->string('days');
            $table->timestamps();
            $table->string('location');
           
            $table->foreign('learner_id')->references('LearnerID')->on('learners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tuition_requests');
    }
};
