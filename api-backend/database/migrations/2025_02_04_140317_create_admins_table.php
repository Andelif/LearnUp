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
        Schema::create('admins', function (Blueprint $table) {
            $table->id('AdminID'); 
            $table->unsignedBigInteger('user_id')->unique(); // Foreign key to users table
            $table->string('full_name');
            $table->string('address')->nullable();
            $table->string('contact_number')->nullable();
            $table->boolean('permission_req')->default(false);
            $table->integer('match_made')->default(0);
            $table->string('task_assigned')->nullable();
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
        Schema::dropIfExists('admins');
    }
};
