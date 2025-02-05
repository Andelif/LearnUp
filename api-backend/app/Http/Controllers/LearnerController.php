<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Learner;

class LearnerController extends Controller
{
    public function index()
    {
        return response()->json(Learner::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'full_name' => 'required|string|max:255',
            'guardian_full_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:15',
            'guardian_contact_number' => 'nullable|string|max:15',
            'gender' => 'required|string|in:Male,Female,Other',
            'address' => 'required|string|max:255',
        ]);

        $learner = Learner::create($request->all());
        return response()->json([
            'message' => 'Learner profile created successfully',
            'learner' => $learner
        ], 201);
    }

    public function show($id)
    {
        return response()->json(Learner::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $learner = Learner::findOrFail($id);
        $learner->update($request->all());
        return response()->json($learner);
    }

    public function destroy($id)
    {
        Learner::destroy($id);
        return response()->json(['message' => 'Learner deleted successfully']);
    }
}
