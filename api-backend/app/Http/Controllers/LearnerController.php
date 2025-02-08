<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Learner;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LearnerController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'learner') {
            return response()->json(['message' => 'Unauthorized: You are not a learner'], 403);
        }
        
        return response()->json(Learner::all());
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'learner') {
            return response()->json(['message' => 'Unauthorized: You are not a learner'], 403);
        }
        
        $request->validate([
            'full_name' => 'required|string|max:255',
            'guardian_full_name' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:15',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'address' => 'nullable|string|max:255',
        ]);

        $learner = Learner::updateOrCreate(
            ['user_id' => $user->id],
            $request->all()
        );

        return response()->json([
            'message' => 'Learner profile updated successfully',
            'learner' => $learner
        ], 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $learner = Learner::findOrFail($id);

        if (!$user || $user->id !== $learner->user_id) {
            return response()->json(['message' => 'Unauthorized: You are not allowed to view this profile'], 403);
        }

        return response()->json($learner);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $learner = Learner::findOrFail($id);

        if (!$user || $user->id !== $learner->user_id) {
            return response()->json(['message' => 'Unauthorized: You cannot update this profile'], 403);
        }

        $learner->update($request->all());
        return response()->json($learner);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $learner = Learner::findOrFail($id);

        if (!$user || $user->id !== $learner->user_id) {
            return response()->json(['message' => 'Unauthorized: You cannot delete this profile'], 403);
        }

        $learner->delete();
        return response()->json(['message' => 'Learner profile deleted successfully']);
    }
}
