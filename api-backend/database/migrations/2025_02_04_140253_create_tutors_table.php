<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE TABLE tutors (
            TutorID BIGINT AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT unsigned NOT NULL,
            full_name VARCHAR(255),
            address VARCHAR(255)  NULL,
            contact_number VARCHAR(255) NULL,
            gender ENUM('Male', 'Female', 'Other')  NULL,
            preferred_salary INT NULL,
            qualification VARCHAR(255) NULL,
            experience VARCHAR(255) NULL,
            currently_studying_in VARCHAR(255) NULL,
            preferred_location VARCHAR(255) NULL,
            preferred_time VARCHAR(255) NULL,
            availability BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP TABLE IF EXISTS tutors");
    }
};
