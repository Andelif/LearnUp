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
        DB::statement("CREATE TABLE tuition_requests (
            TutionID BIGINT AUTO_INCREMENT PRIMARY KEY,
            learner_id BIGINT NOT NULL,
            class VARCHAR(255) not null,
            subjects TEXT not null,
            asked_salary INT not null,
            curriculum VARCHAR(255) not null,
            days VARCHAR(255),
            location VARCHAR(255),
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (learner_id) REFERENCES learners(LearnerID) ON DELETE CASCADE
        )");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP TABLE IF EXISTS tuition_requests");
    }
};
