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
    DB::statement("CREATE TABLE admins (
    AdminID BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED UNIQUE NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    address VARCHAR(255) NULL,
    contact_number VARCHAR(20) NULL,
    permission_req BOOLEAN DEFAULT FALSE,
    match_made INT DEFAULT 0,
    task_assigned VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_admins_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE admins DROP FOREIGN KEY fk_admins_user");
        DB::statement("DROP TABLE IF EXISTS admins");
        
    }
};
