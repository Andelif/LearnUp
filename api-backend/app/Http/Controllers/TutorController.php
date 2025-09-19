<?php

namespace App\Http\Controllers;

use App\Services\TutorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TutorController extends Controller
{
    protected TutorService $tutorService;

    public function __construct(TutorService $tutorService)
    {
        $this->tutorService = $tutorService;
    }

    /** GET /api/tutors  */
    public function index()
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        if ($user->role === 'admin') {
            return response()->json($this->tutorService->getAllTutors(), 200);
        }

        if ($user->role !== 'tutor') {
            return response()->json(['message' => 'Forbidden: not a tutor'], 403);
        }

        $me = $this->tutorService->getTutorByUserId($user->id);
        return response()->json($me, 200);
    }

    /** POST /api/tutors  (create or update current tutor profile) */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'tutor') {
            return response()->json(['message' => 'Forbidden: not a tutor'], 403);
        }

        $validator = Validator::make($request->all(), [
            'full_name'             => 'required|string|max:255',
            'address'               => 'nullable|string|max:255',
            'contact_number'        => 'nullable|string|max:20',
            'gender'                => 'nullable|string|in:male,female,other,Male,Female,Other',
            'preferred_salary'      => 'nullable|integer|min:0',
            'qualification'         => 'nullable|string|max:255',
            'experience'            => 'nullable|string|max:255',
            'currently_studying_in' => 'nullable|string|max:255',
            'preferred_location'    => 'nullable|string|max:255',
            'preferred_time'        => 'nullable|string|max:255',
            'availability'          => 'nullable|boolean',
            'profile_picture'       => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validated = $validator->validated();

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = "/storage/" . $path;
        }

        $row = $this->tutorService->upsertForUser($user->id, $validated);

        return response()->json([
            'message' => 'Tutor profile saved',
            'data'    => $row,
        ], 201);
    }

    /** GET /api/tutors/{userId} */
    public function show($userId)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        if ($user->role !== 'admin' && $user->id != (int)$userId) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $row = $this->tutorService->getTutorByUserId((int)$userId);
        if (!$row) return response()->json(['message' => 'Not found'], 404);

        return response()->json($row, 200);
    }

    /** PUT /api/tutors/{userId} */
    public function update(Request $request, $userId)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $validator = Validator::make($request->all(), [
            'full_name'             => 'sometimes|required|string|max:255',
            'address'               => 'sometimes|nullable|string|max:255',
            'contact_number'        => 'sometimes|nullable|string|max:20',
            'gender'                => 'sometimes|nullable|string|in:male,female,other,Male,Female,Other',
            'preferred_salary'      => 'sometimes|nullable|integer|min:0',
            'qualification'         => 'sometimes|nullable|string|max:255',
            'experience'            => 'sometimes|nullable|string|max:255',
            'currently_studying_in' => 'sometimes|nullable|string|max:255',
            'preferred_location'    => 'sometimes|nullable|string|max:255',
            'preferred_time'        => 'sometimes|nullable|string|max:255',
            'availability'          => 'sometimes|nullable|boolean',
            'profile_picture'       => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validated = $validator->validated();

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = "/storage/" . $path;
        }

        $row = $this->tutorService->upsertForUser((int)$userId, $validated);

        return response()->json([
            'message' => 'Tutor profile updated successfully',
            'data'    => $row,
        ], 200);
    }

    /** DELETE /api/tutors/{userId} */
    public function destroy($userId)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        if ($user->role !== 'admin' && $user->id != (int)$userId) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $deleted = $this->tutorService->deleteByUserId((int)$userId);

        return response()->json([
            'message' => $deleted ? 'Tutor profile deleted successfully' : 'Nothing to delete',
        ], 200);
    }
}
