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

    /**
     * GET /api/tutors
     * - Admin: list all tutors
     * - Tutor: return own profile
     */
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

    /**
     * POST /api/tutors
     * Create or update current tutor’s profile
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'tutor') {
            return response()->json(['message' => 'Forbidden: not a tutor'], 403);
        }

        $validator = Validator::make($request->all(), [
            'full_name'              => 'required|string|max:255',
            'address'                => 'nullable|string|max:255',
            'contact_number'         => 'nullable|string|max:20',
            'gender'                 => 'nullable|string|in:male,female,other,Male,Female,Other',
            'preferred_salary'       => 'nullable|integer|min:0',
            'qualification'          => 'nullable|string|max:255',
            'experience'             => 'nullable|integer|min:0',
            'currently_studying_in'  => 'nullable|string|max:255',
            'preferred_location'     => 'nullable|string|max:255',
            'preferred_time'         => 'nullable|string|max:255',
            'availability'           => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $row = $this->tutorService->upsertForUser($user->id, $validator->validated());

        return response()->json([
            'message' => 'Tutor profile saved',
            'data'    => $row,
        ], 201);
    }

    /**
     * GET /api/tutors/{userId}
     * Only the owner (or admin) can view.
     */
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

    /**
     * PUT /api/tutors/{userId}
     * Only the owner (or admin) can update.
     */
    public function update(Request $request, $userId)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        if ($user->role !== 'admin' && $user->id != (int)$userId) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(), [
            'full_name'              => 'sometimes|required|string|max:255',
            'address'                => 'sometimes|nullable|string|max:255',
            'contact_number'         => 'sometimes|nullable|string|max:20',
            'gender'                 => 'sometimes|nullable|string|in:male,female,other,Male,Female,Other',
            'preferred_salary'       => 'sometimes|nullable|integer|min:0',
            'qualification'          => 'sometimes|nullable|string|max:255',
            'experience'             => 'sometimes|nullable|integer|min:0',
            'currently_studying_in'  => 'sometimes|nullable|string|max:255',
            'preferred_location'     => 'sometimes|nullable|string|max:255',
            'preferred_time'         => 'sometimes|nullable|string|max:255',
            'availability'           => 'sometimes|nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $row = $this->tutorService->upsertForUser((int)$userId, $validator->validated());

        return response()->json([
            'message' => 'Tutor profile updated successfully',
            'data'    => $row,
        ], 200);
    }

    /**
     * DELETE /api/tutors/{userId}
     * Only the owner (or admin).
     */
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
