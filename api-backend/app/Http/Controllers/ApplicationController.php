<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function index()
    {
        return response()->json(Application::all());
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'tutor') {
            return response()->json(['message' => 'Unauthorized: Only tutors can apply for tuition requests'], 403);
        }

        $request->validate([
            'tuition_id' => 'required|exists:tuition_requests,id',
        ]);

        $application = Application::create([
            'tuition_id' => $request->tuition_id,
            'tutor_id' => $user->id, // Associate application with authenticated tutor
        ]);

        return response()->json([
            'message' => 'Application submitted successfully',
            'application' => $application
        ], 201);
    }

    public function show($id)
    {
        return response()->json(Application::findOrFail($id));
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $application = Application::findOrFail($id);

        if (!$user || $user->id !== $application->tutor_id) {
            return response()->json(['message' => 'Unauthorized: Only the tutor who applied can delete this application'], 403);
        }

        $application->delete();
        return response()->json(['message' => 'Application deleted successfully']);
    }
}
