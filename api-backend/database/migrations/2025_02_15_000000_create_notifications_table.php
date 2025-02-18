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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('NotificationID'); 
            $table->unsignedBigInteger('user_id'); // Foreign key to users table
            $table->timestamp('TimeSent')->useCurrent(); 
            $table->text('Message'); 
            $table->enum('Type', ['Tuition Request', 'Application Update', 'New Message', 'Admin Message', 'General']); 
            $table->enum('Status', ['Unread', 'Read'])->default('Unread'); 
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
        Schema::dropIfExists('notifications');
    }
};
