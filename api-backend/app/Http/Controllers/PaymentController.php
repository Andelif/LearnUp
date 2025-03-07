<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TutorConfirmationMail;

class PaymentController extends Controller
{
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,ApplicationID',
            'amount' => 'required|numeric|min:1'
        ]);

        $user = Auth::user();
        
        // Check if the application is already confirmed
        $application = DB::select("SELECT * FROM applications WHERE ApplicationID = ? AND status = 'Confirmed'", [$request->application_id]);

        if (empty($application)) {
            return response()->json(['error' => 'This tuition is not confirmed yet'], 400);
        }

        // Update payment status
        DB::update("UPDATE applications SET payment_status = 'Completed' WHERE ApplicationID = ?", [$request->application_id]);

        // Send confirmation email to tutor
        $tutor = DB::select("SELECT email FROM users WHERE id = (SELECT user_id FROM tutors WHERE TutorID = ?)", [$application[0]->tutor_id]);

        if (!empty($tutor)) {
            Mail::to($tutor[0]->email)->send(new TutorConfirmationMail($application[0]));
        }

        return response()->json(['message' => 'Payment completed successfully, tutor has been notified']);
    }
}
