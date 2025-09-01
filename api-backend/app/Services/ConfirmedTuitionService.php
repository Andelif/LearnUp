<?php

namespace App\Services;

use App\Models\ConfirmedTuition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConfirmedTuitionService
{
    public function getAllConfirmedTuitions()
    {
        // Uses model -> correct table already set to confirmed_tuitions
        return ConfirmedTuition::orderByDesc('ConfirmedTuitionID')->get();
    }

    public function getConfirmedTuitionById(int $id)
    {
        return ConfirmedTuition::find($id);
    }

    /**
     * Create a confirmed tuition:
     * - ensure unique (application_id + tution_id)
     * - set application.status = 'Confirmed'
     */
    public function storeConfirmedTuition(array $data): array
    {
        // Uniqueness check on the pair
        $exists = ConfirmedTuition::where('application_id', $data['application_id'])
            ->where('tution_id', $data['tution_id'])
            ->exists();

        if ($exists) {
            return ['error' => 'This tutor has already been confirmed for this tuition job'];
        }

        $row = DB::transaction(function () use ($data) {
            // Update the application status
            DB::table('applications')
                ->where('ApplicationID', $data['application_id'])
                ->update(['status' => 'Confirmed']);

            // Create confirmed row
            return ConfirmedTuition::create([
                'application_id'  => $data['application_id'],
                'tution_id'       => $data['tution_id'],   // (spelling per your schema)
                'FinalizedSalary' => $data['FinalizedSalary'],
                'FinalizedDays'   => $data['FinalizedDays'],
                'Status'          => $data['Status'],
            ]);
        });

        return ['message' => 'Confirmed Tuition created successfully', 'data' => $row];
    }

    /**
     * For the current tutor, check they are confirmed on this tuition
     * and return a simple voucher payload (you can extend later).
     */
    public function getPaymentVoucher(int $tutionId): array
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'tutor') {
            return ['error' => 'Unauthorized: Only tutors can view this voucher.'];
        }

        $tutorId = DB::table('tutors')->where('user_id', $user->id)->value('TutorID');
        if (!$tutorId) {
            return ['error' => 'Tutor profile not found.'];
        }

        // IMPORTANT: use snake_case table name
        $owns = DB::table('confirmed_tuitions as ct')
            ->join('applications as a', 'ct.application_id', '=', 'a.ApplicationID')
            ->where('ct.tution_id', $tutionId)
            ->where('a.tutor_id', $tutorId)
            ->exists();

        if (!$owns) {
            return ['error' => 'Tuition not found or not confirmed for this tutor.'];
        }

        return [
            'tutorId'   => $tutorId,
            'tution_id' => $tutionId,
            'message'   => 'Tutor confirmed for this tuition',
        ];
    }

    /**
     * Current tutor marks payment as completed for a tution_id.
     * Requires a pre-existing row in payment_vouchers.
     */
    public function markPayment(int $tutionId): array
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'tutor') {
            return ['error' => 'Unauthorized: Only tutors can mark payment.'];
        }

        $tutorId = DB::table('tutors')->where('user_id', $user->id)->value('TutorID');
        if (!$tutorId) {
            return ['error' => 'Tutor profile not found.'];
        }

        // IMPORTANT: use snake_case table name
        $owns = DB::table('confirmed_tuitions as ct')
            ->join('applications as a', 'ct.application_id', '=', 'a.ApplicationID')
            ->where('ct.tution_id', $tutionId)
            ->where('a.tutor_id', $tutorId)
            ->exists();

        if (!$owns) {
            return ['error' => 'You are not confirmed for this tuition.'];
        }

        $updated = DB::table('payment_vouchers')
            ->where('tution_id', $tutionId)
            ->update(['status' => 'Completed']);

        if ($updated > 0) {
            return ['message' => 'Payment marked successfully.'];
        }
        return ['error' => 'Failed to mark payment (no voucher row found or already completed).'];
    }

    public function deleteConfirmedTuition(int $id): array
    {
        $deleted = ConfirmedTuition::where('ConfirmedTuitionID', $id)->delete();
        return ['message' => $deleted ? 'Confirmed Tuition deleted successfully' : 'Nothing to delete'];
    }
}
