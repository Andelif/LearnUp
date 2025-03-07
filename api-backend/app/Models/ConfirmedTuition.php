<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfirmedTuition;

class ConfirmedTuitionController extends Controller
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