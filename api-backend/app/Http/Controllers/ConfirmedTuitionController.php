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

        // AND 
        // tution_id = (SELECT TutionID from tuition_requests WHERE LearnerID = 
        //                 (SELECT LearnerID from learners WHERE user_id = ?) 
                              
        //             )


        $userConfirmed = DB::select("SELECT application_id FROM ConfirmedTuitions WHERE application_id = ?", [
            $request->application_id,
        
        ]);
            
        if (!empty($userConfirmed)) {
            $exist = $userConfirmed[0]->application_id;
        } else {
            $exist = 0; 
        }

        if($exist==0)
        {

            DB::update("UPDATE applications SET status = 'Confirmed' WHERE ApplicationID = ?", [$request->application_id]);
           
            DB::insert("INSERT INTO ConfirmedTuitions (application_id, tution_id, FinalizedSalary, FinalizedDays, Status) VALUES (?, ?, ?, ?, ?)", [
                $request->application_id,
                $request->tution_id,
                $request->FinalizedSalary,
                $request->FinalizedDays,
                $request->Status
            ]);
            (new PaymentController())->initiatePayment($request);

              return response()->json(['message' => 'Confirmed Tuition created and payment processed successfully'], 201);
    
            
        }
        
       



        
    }

    // Update a confirmed tuition
    public function update(Request $request, $id)
    {
        $request->validate([
            'FinalizedSalary' => 'nullable|numeric',
            'FinalizedDays' => 'nullable|string',
            'Status' => 'nullable|in:Ongoing,Ended'
        ]);

        DB::update("UPDATE ConfirmedTuitions SET FinalizedSalary = COALESCE(?, FinalizedSalary), FinalizedDays = COALESCE(?, FinalizedDays), Status = COALESCE(?, Status) WHERE ConfirmedTuitionID = ?", [
            $request->FinalizedSalary,
            $request->FinalizedDays,
            $request->Status,
            $id
        ]);

        return response()->json(['message' => 'Confirmed Tuition updated successfully']);
    }

    // Delete a confirmed tuition
    public function destroy($id)
    {
        DB::delete("DELETE FROM ConfirmedTuitions WHERE ConfirmedTuitionID = ?", [$id]);
        return response()->json(['message' => 'Confirmed Tuition deleted successfully']);
    }
}
