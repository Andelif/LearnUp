<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tutor;
use Illuminate\Support\Facades\Auth;

class TutorController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'tutor') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(Tutor::all());
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'tutor') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact_number' => 'required|string|max:15',
            'gender' => 'required|string|in:Male,Female,Other',
            'preferred_salary' => 'nullable|numeric|min:0',
            'qualification' => 'required|string|max:255',
            'experience' => 'required|string|max:255',
            'currently_studying_in' => 'required|string|max:255',
        ]);

        $tutor = Tutor::updateOrCreate(
            ['user_id' => Auth::id()],
            $request->all()
        );

        return response()->json([
            'message' => 'Tutor profile updated successfully',
            'tutor' => $tutor
        ], 201);
    }

    public function show($id)
    {
        $tutor = Tutor::findOrFail($id);

        if (Auth::id() !== $tutor->user_id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($tutor);
    }

    public function update(Request $request, $id)
    {
        $tutor = Tutor::findOrFail($id);

        if (Auth::id() !== $tutor->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $tutor->update($request->all());
        return response()->json($tutor);
    }

    public function destroy($id)
    {
        $tutor = Tutor::findOrFail($id);

        if (Auth::id() !== $tutor->user_id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $tutor->delete();
        return response()->json(['message' => 'Tutor deleted successfully']);
    }
}
