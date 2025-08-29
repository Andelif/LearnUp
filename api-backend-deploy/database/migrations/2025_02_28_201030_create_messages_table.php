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
        DB::statement("CREATE TABLE messages (
            MessageID BIGINT AUTO_INCREMENT PRIMARY KEY,
            SentBy BIGINT unsigned NOT NULL, -- Foreign key referencing users table
            SentTo BIGINT unsigned NOT NULL, -- Foreign key referencing users table
            Content TEXT NOT NULL,
            TimeStamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            Status ENUM('Delivered', 'Failed', 'Seen') DEFAULT 'Delivered',
            FOREIGN KEY (SentBy) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (SentTo) REFERENCES users(id) ON DELETE CASCADE
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
        DB::statement("DROP TABLE IF EXISTS messages");
    }
};
