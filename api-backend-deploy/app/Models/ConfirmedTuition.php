<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;;

class ConfirmedTuition extends Model
{
    public function index()
    {
        return response()->json(ConfirmedTuition::getAllConfirmedTuitions());
    }

    public function show($id)
    {
        return response()->json(ConfirmedTuition::getConfirmedTuitionById($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ApplicationID' => 'required|integer',
            'TuitionID' => 'required|integer',
            'FinalizedSalary' => 'required|numeric',
            'FinalizedDays' => 'required|string',
            'Status' => 'required|in:Ongoing,Ended'
        ]);
        
        ConfirmedTuition::createConfirmedTuition($data);
        return response()->json(['message' => 'Confirmed Tuition created successfully']);
    }
}