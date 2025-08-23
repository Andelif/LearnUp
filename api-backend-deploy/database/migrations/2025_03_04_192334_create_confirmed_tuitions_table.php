<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateConfirmedTuitionsTable extends Migration
{
    public function up()
    {
        DB::statement("CREATE TABLE ConfirmedTuitions (
            ConfirmedTuitionID BIGINT AUTO_INCREMENT PRIMARY KEY,
            application_id BIGINT NOT NULL,
            tution_id BIGINT NOT NULL,
            FinalizedSalary DECIMAL(10,2) NOT NULL,
            FinalizedDays VARCHAR(255) NOT NULL,
            Status ENUM('Ongoing', 'Ended') DEFAULT 'Ongoing',
            ConfirmedDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (application_id) REFERENCES applications(ApplicationID) ON DELETE CASCADE,
            FOREIGN KEY (tution_id) REFERENCES tuition_requests(TutionID) ON DELETE CASCADE
        )ENGINE=InnoDB");
    }

    public function down()
    {
        DB::statement("DROP TABLE IF EXISTS ConfirmedTuitions");
    }
}