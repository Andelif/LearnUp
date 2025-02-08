<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TuitionRequest;
use Illuminate\Support\Facades\Auth;

class TuitionRequestController extends Controller
{
    public function index()
    {
        return response()->json(TuitionRequest::all());
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'learner') {
            return response()->json(['message' => 'Unauthorized: Only learners can create tuition requests'], 403);
        }

        $request->validate([
            'class' => 'required|string|max:255',
            'subjects' => 'required|string',
            'asked_salary' => 'required|numeric',
            'curriculum' => 'required|string',
            'days' => 'required|string',
        ]);

        $tuitionRequest = TuitionRequest::create([
            'learner_id' => $user->id,  // Associate request with authenticated learner
            'class' => $request->class,
            'subjects' => $request->subjects,
            'asked_salary' => $request->asked_salary,
            'curriculum' => $request->curriculum,
            'days' => $request->days,
        ]);

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
        $user = Auth::user();
        $tuitionRequest = TuitionRequest::findOrFail($id);

        if (!$user || $user->id !== $tuitionRequest->learner_id) {
            return response()->json(['message' => 'Unauthorized: Only the learner who created this request can update it'], 403);
        }

        $tuitionRequest->update($request->all());
        return response()->json($tuitionRequest);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $tuitionRequest = TuitionRequest::findOrFail($id);

        if (!$user || $user->id !== $tuitionRequest->learner_id) {
            return response()->json(['message' => 'Unauthorized: Only the learner who created this request can delete it'], 403);
        }

        $tuitionRequest->delete();
        return response()->json(['message' => 'Tuition request deleted successfully']);
    }
}
