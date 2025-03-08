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

        // Update the payment status to 'Completed' (dummy process)
        DB::update("UPDATE applications SET payment_status = 'Completed' WHERE ApplicationID = ?", [$request->application_id]);

        // Fetch tutor details to send the confirmation email
        $tutor = DB::select("SELECT email FROM users WHERE id = (SELECT user_id FROM tutors WHERE TutorID = ?)", [$application[0]->TutorID]);

        if (!empty($tutor)) {
            // Send the confirmation email with the dummy payment link
            Mail::to($tutor[0]->email)->send(new TutorConfirmationMail($application[0]));
        }

        return response()->json(['message' => 'Payment completed successfully (dummy process), tutor has been notified']);
    }
}
