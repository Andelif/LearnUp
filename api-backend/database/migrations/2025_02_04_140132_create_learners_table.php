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
        Schema::create('learners', function (Blueprint $table) {
            $table->id('LearnerID'); 
            $table->unsignedBigInteger('user_id'); // Foreign key to users table
            $table->string('full_name'); 
            $table->string('guardian_full_name')->nullable(); 
            $table->string('contact_number')->nullable(); 
            $table->string('guardian_contact_number')->nullable(); 
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable(); 
            $table->text('address')->nullable(); 
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
        Schema::dropIfExists('learners');
    }
};
