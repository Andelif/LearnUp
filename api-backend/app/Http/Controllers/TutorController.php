<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tutor;

class TutorController extends Controller
{
    public function index()
    {
        return response()->json(Tutor::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'full_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact_number' => 'required|string|max:15',
            'gender' => 'required|string|in:Male,Female,Other',
            'preferred_salary' => 'nullable|numeric|min:0',
            'qualification' => 'required|string|max:255',
            'experience' => 'required|string|max:255',
            'currently_studying_in' => 'required|string|max:255',
            'preferred_location' => 'nullable|string|max:255',
            'preferred_time' => 'nullable|string|max:255',
            'availability' => 'required|boolean'
        ]);

        $tutor = Tutor::create($request->all());
        return response()->json([
            'message' => 'Tutor profile created successfully',
            'tutor' => $tutor
        ], 201);
    }

    public function show($id)
    {
        return response()->json(Tutor::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $tutor = Tutor::findOrFail($id);
        $tutor->update($request->all());
        return response()->json($tutor);
    }

    public function destroy($id)
    {
        Tutor::destroy($id);
        return response()->json(['message' => 'Tutor deleted successfully']);
    }
}
