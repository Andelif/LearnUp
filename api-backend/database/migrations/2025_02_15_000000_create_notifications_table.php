<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('NotificationID');

            // FK → users.id
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Timezone-aware; defaults to now
            $table->timestampTz('TimeSent')->useCurrent();

            $table->text('Message');

            // Enums become CHECK constraints on Postgres
            $table->enum('Type', [
                'Tuition Request',
                'Application Update',
                'New Message',
                'Admin Message',
                'General',
            ]);

            $table->enum('Status', ['Unread', 'Read'])->default('Unread');

            $table->string('view');

            // TZ-aware created_at/updated_at
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
