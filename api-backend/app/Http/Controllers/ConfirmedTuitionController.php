<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PaymentController;
class ConfirmedTuitionController extends Controller
{
    // Get all confirmed tuitions
    public function index()
    {
        $confirmedTuitions = DB::select("SELECT * FROM ConfirmedTuitions");
        return response()->json($confirmedTuitions);
    }

    // Get a single confirmed tuition by ID
    public function show($id)
    {
        $confirmedTuition = DB::select("SELECT * FROM ConfirmedTuitions WHERE ConfirmedTuitionID = ?", [$id]);
        return response()->json($confirmedTuition);
    }

    // Create a new confirmed tuition
    public function store(Request $request)
{
    $request->validate([
        'application_id' => 'required|exists:applications,ApplicationID',
        'tution_id' => 'required|exists:tuition_requests,TutionID',
        'FinalizedSalary' => 'required|numeric',
        'FinalizedDays' => 'required|string',
        'Status' => 'required|in:Ongoing,Ended'
    ]);

    $user = Auth::user();

    // Check if the same tutor has already been confirmed for the SAME tuition job
    $existing = DB::table('ConfirmedTuitions')
        ->where('application_id', $request->application_id)
        ->where('tution_id', $request->tution_id)
        ->exists();

    if ($existing) {
        return response()->json(['error' => 'This tutor has already been confirmed for this tuition job'], 400);
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

    return response()->json(['message' => 'Confirmed Tuition created successfully'], 201);
}
        
    

    // Update a confirmed tuition
    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'FinalizedSalary' => 'nullable|numeric',
    //         'FinalizedDays' => 'nullable|string',
    //         'Status' => 'nullable|in:Ongoing,Ended'
    //     ]);

    //     DB::update("UPDATE ConfirmedTuitions SET FinalizedSalary = COALESCE(?, FinalizedSalary), FinalizedDays = COALESCE(?, FinalizedDays), Status = COALESCE(?, Status) WHERE ConfirmedTuitionID = ?", [
    //         $request->FinalizedSalary,
    //         $request->FinalizedDays,
    //         $request->Status,
    //         $id
    //     ]);

    //     return response()->json(['message' => 'Confirmed Tuition updated successfully']);
    // }
    public function getPaymentVoucher($tutionId)
    {
        $user = Auth::user(); // Get the authenticated user
        if ($user->role !== 'Tutor') {
            return response()->json(['error' => 'Unauthorized: Only tutors can view this voucher.'], 403);
        }
    
        // Retrieve tutor_id from Tutors table using user_id
        $tutor = DB::selectOne("SELECT TutorID FROM Tutors WHERE user_id = ?", [$user->id]);
        if (!$tutor) {
            return response()->json(['error' => 'Tutor profile not found.'], 404);
        }
        $tutorId = $tutor->TutorID;

    // Check if the user is the confirmed tutor for this tuition
      $confirmed = DB::selectOne("
       SELECT COUNT(*) AS confirmed
       FROM ConfirmedTuitions
      WHERE tutor_id = ? AND tution_id = ?", [$tutorId, $tutionId]);

      if ($confirmed->confirmed > 0) {
        return response()->json(['error' => 'You are not confirmed for this tuition.'], 403);
    }

    // Fetch the payment voucher details
    $invoice = [
        'invoice_number' => 'INV-' . strtoupper(uniqid()),
        'tutor_name' => $confirmedTuition->tutor_name,
        'learner_name' => $confirmedTuition->learner_name,
        'session_price' => $confirmedTuition->FinalizedSalary,
        'session_days' => $confirmedTuition->FinalizedDays,
        'total_amount' => $confirmedTuition->FinalizedSalary, // Example, can multiply by days if necessary
        'session_date' => now()->format('Y-m-d H:i:s')
    ];

        if (!$voucher) {
             return response()->json(['error' => 'No payment voucher found.'], 404);
        }

         return response()->json($invoice,200);
    }
    public function markPayment(Request $request, $tutionId)
    {
        // Check if the user is a tutor
        $user = Auth::user();
        if ($user->role !== 'Tutor') {
            return response()->json(['error' => 'Unauthorized: Only tutors can mark payment.'], 403);
        }

        // Retrieve tutor_id from Tutors table using user_id
        $tutor = DB::selectOne("SELECT TutorID FROM Tutors WHERE user_id = ?", [$user->id]);
        if (!$tutor) {
            return response()->json(['error' => 'Tutor profile not found.'], 404);
        }
        $tutorId = $tutor->TutorID;

        // Check if this tutor is associated with the confirmed tuition
        $confirmed = DB::selectOne("
            SELECT COUNT(*) AS confirmed
            FROM ConfirmedTuitions
            WHERE tutor_id = ? AND tution_id = ?", [$tutorId, $tutionId]);

        if ($confirmed->confirmed == 0) {
            return response()->json(['error' => 'You are not confirmed for this tuition.'], 403);
        }

        // Mark the payment status as 'Completed' or 'Processed' (or any other status you want)
        $update = DB::table('payment_vouchers')
            ->where('tution_id', $tutionId)
            ->update(['status' => 'Completed']);

        if ($update) {
            return response()->json(['message' => 'Payment marked successfully.']);
        } else {
            return response()->json(['error' => 'Failed to mark payment.'], 400);
        }
    }
    

    // Delete a confirmed tuition
    public function destroy($id)
    {
        DB::delete("DELETE FROM ConfirmedTuitions WHERE ConfirmedTuitionID = ?", [$id]);
        return response()->json(['message' => 'Confirmed Tuition deleted successfully']);
    }
}
