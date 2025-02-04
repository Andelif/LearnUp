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
            $table->id('TutorID'); 
            $table->unsignedBigInteger('user_id'); // Foreign key to users table
            $table->string('full_name'); 
            $table->string('address')->nullable(); 
            $table->string('contact_number')->nullable(); 
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable(); 
            $table->integer('preferred_salary')->nullable(); 
            $table->string('qualification')->nullable(); 
            $table->integer('experience')->nullable(); 
            $table->string('currently_studying_in')->nullable(); 
            $table->string('preferred_location')->nullable(); 
            $table->string('preferred_time')->nullable(); 
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
