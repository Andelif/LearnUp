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
        DB::statement("CREATE TABLE learners (
            LearnerID BIGINT AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT unsigned NOT NULL,
            full_name VARCHAR(255),
            guardian_full_name VARCHAR(255) NULL,
            contact_number VARCHAR(255) NULL,
            guardian_contact_number VARCHAR(255) not NULL,
            gender ENUM('Male', 'Female', 'Other') not NULL,
            address TEXT not NULL,
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
        DB::statement("DROP TABLE IF EXISTS learners");
    }
};
