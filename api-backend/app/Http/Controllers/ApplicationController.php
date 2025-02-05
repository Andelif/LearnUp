<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;

class ApplicationController extends Controller
{
    public function index()
    {
        return response()->json(Application::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'tution_id' => 'required|exists:tuition_requests,TutionID',
            'learner_id' => 'required|exists:learners,LearnerID',
            'tutor_id' => 'required|exists:tutors,TutorID',
        ]);

        $application = Application::create($request->all());
        return response()->json([
            'message' => 'Application submitted successfully',
            'application' => $application
        ], 201);
    }

    public function show($id)
    {
        return response()->json(Application::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        $application->update($request->all());
        return response()->json($application);
    }

    public function destroy($id)
    {
        Application::destroy($id);
        return response()->json(['message' => 'Application deleted successfully']);
    }
}
