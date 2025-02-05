<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TuitionRequest;

class TuitionRequestController extends Controller
{
    public function index()
    {
        return response()->json(TuitionRequest::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'learner_id' => 'required|exists:learners,LearnerID',
            'class' => 'required|string|max:255',
            'subjects' => 'required|string',
            'asked_salary' => 'required|numeric',
            'curriculum' => 'required|string',
            'days' => 'required|string',
        ]);

        $tuitionRequest = TuitionRequest::create($request->all());
        return response()->json([
            'message' => 'Tuition request created successfully',
            'tuition_request' => $tuitionRequest
        ], 201);
    }

    public function show($id)
    {
        return response()->json(TuitionRequest::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $tuitionRequest = TuitionRequest::findOrFail($id);
        $tuitionRequest->update($request->all());
        return response()->json($tuitionRequest);
    }

    public function destroy($id)
    {
        TuitionRequest::destroy($id);
        return response()->json(['message' => 'Tuition request deleted successfully']);
    }
}
