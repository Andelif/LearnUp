<?php

namespace App\Services;

use App\Models\PaymentVoucher;
use App\Models\ConfirmedTuition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MediaFeeService
{
    const MEDIA_FEE_PERCENTAGE = 30; // 30% media fee
    const TUTOR_PERCENTAGE = 70;     // 70% for tutor

    /**
     * Calculate media fee and tutor amount from a total salary.
     *
     * @param float $totalSalary
     * @return array ['media_fee_amount' => float, 'tutor_amount' => float]
     */
    public function calculateFee(float $totalSalary): array
    {
        $mediaFee = round(($totalSalary * self::MEDIA_FEE_PERCENTAGE) / 100, 2);
        $tutorAmount = round(($totalSalary * self::TUTOR_PERCENTAGE) / 100, 2);

        return [
            'media_fee_amount' => $mediaFee,
            'tutor_amount' => $tutorAmount,
        ];
    }

    /**
     * Generate a unique voucher id.
     *
     * @return string
     */
    public function generateUniqueVoucherId(): string
    {
        do {
            $voucherId = 'PV-' . strtoupper(uniqid());
        } while (PaymentVoucher::where('voucher_id', $voucherId)->exists());

        return $voucherId;
    }

    /**
     * Create a payment voucher for a confirmed tuition.
     *
     * @param int $confirmedTuitionID
     * @return array
     */
    public function createPaymentVoucher(int $confirmedTuitionID): array
    {
        try {
            $confirmedTuition = ConfirmedTuition::find($confirmedTuitionID);
            if (!$confirmedTuition) {
                return ['error' => 'Confirmed tuition not found'];
            }

            $tuitionRequest = $confirmedTuition->tuitionRequest ?? null;
            $application = $confirmedTuition->application ?? null;

            if (!$tuitionRequest || !$application) {
                return ['error' => 'Related data not found'];
            }

            // Check for existing voucher
            $existingVoucher = PaymentVoucher::where('tution_id', $confirmedTuition->tution_id)
                ->where('tutor_id', $application->tutor_id)
                ->first();

            if ($existingVoucher) {
                return ['error' => 'Payment voucher already exists for this tuition'];
            }

            // Flexible salary retrieval (handles different attribute naming conventions)
            $totalSalary = $confirmedTuition->FinalizedSalary
                ?? $confirmedTuition->finalized_salary
                ?? $confirmedTuition->total_salary
                ?? 0;

            if ($totalSalary <= 0) {
                return ['error' => 'Invalid finalized salary'];
            }

            $fees = $this->calculateFee((float) $totalSalary);

            DB::beginTransaction();
            $paymentVoucher = PaymentVoucher::create([
                'tution_id'        => $confirmedTuition->tution_id,
                'tutor_id'         => $application->tutor_id,
                'learner_id'       => $application->learner_id,
                'total_salary'     => $totalSalary,
                'media_fee_amount' => $fees['media_fee_amount'],
                'tutor_amount'     => $fees['tutor_amount'],
                'voucher_id'       => $this->generateUniqueVoucherId(),
                'status'           => 'Pending',
                'due_date'         => now()->addDays(7),
            ]);
            DB::commit();

            return [
                'message' => 'Payment voucher created successfully',
                'data' => $paymentVoucher,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => 'Failed to create payment voucher: ' . $e->getMessage()];
        }
    }

    /**
     * Return voucher info for a tutor (authorization via Auth).
     *
     * @param int $tutionId
     * @return array
     */
    public function getPaymentVoucherForTutor(int $tutionId): array
    {
        $user = Auth::user();
        if (!$user || ($user->role ?? null) !== 'tutor') {
            return ['error' => 'Unauthorized: Only tutors can view payment vouchers'];
        }

        $tutorId = DB::table('tutors')->where('user_id', $user->id)->value('TutorID');
        if (!$tutorId) {
            return ['error' => 'Tutor profile not found'];
        }

        $voucher = PaymentVoucher::with(['learner', 'tution.tuitionRequest'])
            ->where('tution_id', $tutionId)
            ->where('tutor_id', $tutorId)
            ->first();

        if (!$voucher) {
            return ['error' => 'Payment voucher not found'];
        }

        return [
            'voucher_id'      => $voucher->voucher_id,
            'total_salary'    => $voucher->total_salary,
            'media_fee_amount'=> $voucher->media_fee_amount,
            'tutor_amount'    => $voucher->tutor_amount,
            'status'          => $voucher->status,
            'due_date'        => $voucher->due_date,
            'paid_at'         => $voucher->paid_at,
            'payment_method'  => $voucher->payment_method,
            'transaction_id'  => $voucher->transaction_id,
            'payment_notes'   => $voucher->payment_notes,
            'tuition_details' => [
                'class'    => $voucher->tution->tuitionRequest->class ?? 'N/A',
                'subjects' => $voucher->tution->tuitionRequest->subjects ?? 'N/A',
                'days'     => $voucher->tution->tuitionRequest->days ?? 'N/A',
            ],
            'learner_details' => [
                'name'    => $voucher->learner->full_name ?? 'N/A',
                'contact' => $voucher->learner->contact_number ?? 'N/A',
            ],
            'is_overdue'      => method_exists($voucher, 'isOverdue') ? $voucher->isOverdue() : false,
        ];
    }

    /**
     * Tutor marks a voucher as paid.
     *
     * @param int $tutionId
     * @param array $paymentData
     * @return array
     */
    public function markPaymentCompleted(int $tutionId, array $paymentData = []): array
    {
        $user = Auth::user();
        if (!$user || ($user->role ?? null) !== 'tutor') {
            return ['error' => 'Unauthorized: Only tutors can mark payment'];
        }

        $tutorId = DB::table('tutors')->where('user_id', $user->id)->value('TutorID');
        if (!$tutorId) {
            return ['error' => 'Tutor profile not found'];
        }

        $voucher = PaymentVoucher::where('tution_id', $tutionId)
            ->where('tutor_id', $tutorId)
            ->first();

        if (!$voucher) {
            return ['error' => 'Payment voucher not found'];
        }

        if ($voucher->status === 'Completed') {
            return ['error' => 'Payment already completed'];
        }

        // use model helper (added in PaymentVoucher)
        $voucher->markAsPaid(
            $paymentData['payment_method'] ?? null,
            $paymentData['transaction_id'] ?? null,
            $paymentData['payment_notes'] ?? null
        );

        return [
            'message' => 'Payment marked as completed successfully',
            'data' => $voucher->fresh()
        ];
    }
}
