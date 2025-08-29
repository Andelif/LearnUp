<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ConfirmedTuitionService
{
    public function getAllConfirmedTuitions()
    {
        return DB::select("SELECT * FROM ConfirmedTuitions");
    }

    public function getConfirmedTuitionById($id)
    {
        return DB::select("SELECT * FROM ConfirmedTuitions WHERE ConfirmedTuitionID = ?", [$id]);
    }

    public function storeConfirmedTuition($request)
    {
        // Validate request
        $request->validate([
            'application_id' => 'required|exists:applications,ApplicationID',
            'tution_id' => 'required|exists:tuition_requests,TutionID',
            'FinalizedSalary' => 'required|numeric',
            'FinalizedDays' => 'required|string',
            'Status' => 'required|in:Ongoing,Ended'
        ]);

        // Check if the same tutor has already been confirmed for the SAME tuition job
        $existing = DB::table('ConfirmedTuitions')
            ->where('application_id', $request->application_id)
            ->where('tution_id', $request->tution_id)
            ->exists();

        if ($existing) {
            return ['error' => 'This tutor has already been confirmed for this tuition job'];
        }

        // Update application status to "Confirmed"
        DB::table('applications')->where('ApplicationID', $request->application_id)->update(['status' => 'Confirmed']);

        // Insert new confirmed tuition
        DB::table('ConfirmedTuitions')->insert([
            'application_id' => $request->application_id,
            'tution_id' => $request->tution_id,
            'FinalizedSalary' => $request->FinalizedSalary,
            'FinalizedDays' => $request->FinalizedDays,
            'Status' => $request->Status
        ]);

        return ['message' => 'Confirmed Tuition created successfully'];
    }

    public function getPaymentVoucher($tutionId)
    {
        $user = Auth::user();
        if ($user->role !== 'tutor') {
            return ['error' => 'Unauthorized: Only tutors can view this voucher.'];
        }

        // Retrieve tutor_id from Tutors table using user_id
        $tutor = DB::selectOne("SELECT TutorID FROM tutors WHERE user_id = ?", [$user->id]);
        if (!$tutor) {
            return ['error' => 'Tutor profile not found.'];
        }

        // Check if the user is the confirmed tutor for this tuition
        $confirmed = DB::selectOne("SELECT application_id FROM ConfirmedTuitions WHERE tution_id = ?", [$tutionId]);

        if (!$confirmed) {
            return ['error' => 'Tuition not found or not confirmed.'];
        }

        $applicationId = $confirmed->application_id;

        // Fetch the tutor_id using application_id
        $tutor = DB::selectOne("SELECT tutor_id FROM applications WHERE ApplicationID = ?", [$applicationId]);

        if (!$tutor) {
            return ['error' => 'Tutor not found for this application.'];
        }

        // Return the tutorId and other information
        return [
            'tutorId' => $tutor->tutor_id,
            'message' => 'Tutor confirmed for this tuition'
        ];
    }

    public function markPayment($tutionId)
    {
        // Check if the user is a tutor
        $user = Auth::user();
        if ($user->role !== 'Tutor') {
            return ['error' => 'Unauthorized: Only tutors can mark payment.'];
        }

        // Retrieve tutor_id from Tutors table using user_id
        $tutor = DB::selectOne("SELECT TutorID FROM Tutors WHERE user_id = ?", [$user->id]);
        if (!$tutor) {
            return ['error' => 'Tutor profile not found.'];
        }

        // Check if this tutor is associated with the confirmed tuition
        $confirmed = DB::selectOne("SELECT COUNT(*) AS confirmed FROM ConfirmedTuitions WHERE tutor_id = ? AND tution_id = ?", [$tutor->TutorID, $tutionId]);

        if ($confirmed->confirmed == 0) {
            return ['error' => 'You are not confirmed for this tuition.'];
        }

        // Mark the payment status as 'Completed'
        $update = DB::table('payment_vouchers')
            ->where('tution_id', $tutionId)
            ->update(['status' => 'Completed']);

        if ($update) {
            return ['message' => 'Payment marked successfully.'];
        } else {
            return ['error' => 'Failed to mark payment.'];
        }
    }

    public function deleteConfirmedTuition($id)
    {
        DB::delete("DELETE FROM ConfirmedTuitions WHERE ConfirmedTuitionID = ?", [$id]);
        return ['message' => 'Confirmed Tuition deleted successfully'];
    }
}
