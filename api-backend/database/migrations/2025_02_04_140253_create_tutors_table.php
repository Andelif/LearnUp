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
        Schema::create('tutors', function (Blueprint $table) {
            $table->id('TutorID'); // This will be the UserID (FK)
            $table->unsignedBigInteger('user_id')->unique(); // Foreign key to users table
            $table->string('full_name');
            $table->string('address');
            $table->string('contact_number');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->integer('preferred_salary');
            $table->string('qualification');
            $table->integer('experience');
            $table->string('currently_studying_in');
            $table->string('preferred_location');
            $table->string('preferred_time');
            $table->boolean('availability')->default(true);
            $table->timestamps();
    
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tutors');
    }
};
