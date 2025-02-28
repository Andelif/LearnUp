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
        DB::statement("
        CREATE TABLE applications (
            ApplicationID BIGINT AUTO_INCREMENT PRIMARY KEY,
            tution_id BIGINT NOT NULL, -- Foreign key to tuition_requests table
            learner_id BIGINT NOT NULL, -- Foreign key to learners table
            tutor_id BIGINT NOT NULL, -- Foreign key to tutors table
            matched BOOLEAN NOT NULL DEFAULT FALSE,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (tution_id) REFERENCES tuition_requests(TutionID) ON DELETE CASCADE,
            FOREIGN KEY (learner_id) REFERENCES learners(LearnerID) ON DELETE CASCADE,
            FOREIGN KEY (tutor_id) REFERENCES tutors(TutorID) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applications');
    }
};
