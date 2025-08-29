<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id('MessageID');

            // Foreign keys to users
            $table->foreignId('SentBy')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->foreignId('SentTo')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->text('Content');

            // TZ-aware timestamp with default now()
            $table->timestampTz('TimeStamp')->useCurrent();

            // ENUM → CHECK in Postgres
            $table->enum('Status', ['Delivered', 'Failed', 'Seen'])
                  ->default('Delivered');

            // If you want created_at/updated_at as well
            // $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
