<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_vouchers', function (Blueprint $table) {
            // Primary key
            $table->id('PaymentVoucherID');
            
            // Foreign keys - using unsignedBigInteger for custom primary keys
            $table->unsignedBigInteger('tution_id');
            $table->unsignedBigInteger('tutor_id');
            $table->unsignedBigInteger('learner_id');

            // Payment details
            $table->decimal('total_salary', 10, 2);
            $table->decimal('media_fee_amount', 10, 2);
            $table->decimal('tutor_amount', 10, 2);
            $table->string('voucher_id')->unique();
            
            // Status and payment info
            $table->enum('status', ['Pending', 'Completed', 'Failed', 'Cancelled'])->default('Pending');
            $table->enum('payment_method', ['Bank_Transfer', 'Mobile_Banking', 'Cash', 'Other'])->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('payment_notes')->nullable();
            
            // Dates
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // Add foreign key constraints after table creation
        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->foreign('tution_id')->references('tution_id')->on('confirmed_tuitions')->onDelete('cascade');
            $table->foreign('tutor_id')->references('TutorID')->on('tutors')->onDelete('cascade');
            $table->foreign('learner_id')->references('LearnerID')->on('learners')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_vouchers');
    }
};
