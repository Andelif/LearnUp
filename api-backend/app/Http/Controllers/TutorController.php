<?php

namespace App\Http\Controllers;

use App\Services\TutorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class TutorController extends Controller
{
    protected TutorService $tutorService;

    public function __construct(TutorService $tutorService)
    {
        $this->tutorService = $tutorService;
    }

    /** GET /api/tutors */
    public function index()
    {
        $user = Auth::user();
        Log::info("TutorController@index called", ['user' => $user]);

        if (!$user) {
            Log::warning("Unauthorized access to tutors index");
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($user->role === 'admin') {
            Log::info("Admin fetching all tutors");
            return response()->json($this->tutorService->getAllTutors(), 200);
        }

        if ($user->role !== 'tutor') {
            Log::warning("Forbidden: user tried to fetch tutor index", ['role' => $user->role]);
            return response()->json(['message' => 'Forbidden: not a tutor'], 403);
        }

        $me = $this->tutorService->getTutorByUserId($user->id);
        Log::info("Tutor profile fetched", ['user_id' => $user->id, 'profile' => $me]);
        return response()->json($me, 200);
    }

    /** POST /api/tutors */
    public function store(Request $request)
    {
        $user = Auth::user();
        Log::info("TutorController@store called", ['user' => $user, 'input' => $request->all()]);

        if (!$user || $user->role !== 'tutor') {
            Log::warning("Forbidden store attempt", ['user' => $user]);
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
            Log::warning("Validation failed for tutor store", ['errors' => $validator->errors()]);
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 400);
        }

        $validated = $validator->validated();

        try {
            if ($request->hasFile('profile_picture')) {
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                $validated['profile_picture'] = "/storage/" . $path;
                Log::info("Profile picture uploaded for tutor", ['path' => $validated['profile_picture']]);
            } else {
                Log::info("No profile picture uploaded in tutor store request");
            }

            $row = $this->tutorService->upsertForUser($user->id, $validated);

            Log::info("Tutor profile saved successfully", ['user_id' => $user->id, 'profile' => $row]);
            return response()->json([
                'message' => 'Tutor profile saved successfully',
                'data'    => $row,
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error saving tutor profile", ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Server error while saving tutor profile',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /** GET /api/tutors/{userId} */
    public function show($userId)
    {
        $user = Auth::user();
        Log::info("TutorController@show called", ['auth_user' => $user, 'target_user' => $userId]);

        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        if ($user->role !== 'admin' && $user->id != (int)$userId) {
            Log::warning("Forbidden show attempt", ['auth_user' => $user->id, 'target_user' => $userId]);
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $row = $this->tutorService->getTutorByUserId((int)$userId);
        if (!$row) {
            Log::warning("Tutor profile not found", ['target_user' => $userId]);
            return response()->json(['message' => 'Not found'], 404);
        }

        Log::info("Tutor profile fetched via show", ['target_user' => $userId, 'profile' => $row]);
        return response()->json($row, 200);
    }

    /** PUT /api/tutors/{userId} */
    public function update(Request $request, $userId)
    {
        $user = Auth::user();
        Log::info("TutorController@update called", ['auth_user' => $user, 'target_user' => $userId, 'input' => $request->all()]);

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
            Log::warning("Validation failed for tutor update", ['errors' => $validator->errors()]);
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 400);
        }

        $validated = $validator->validated();

        try {
            if ($request->hasFile('profile_picture')) {
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                $validated['profile_picture'] = "/storage/" . $path;
                Log::info("Profile picture updated for tutor", ['path' => $validated['profile_picture']]);
            }

            $row = $this->tutorService->upsertForUser((int)$userId, $validated);

            Log::info("Tutor profile updated successfully", ['user_id' => $userId, 'profile' => $row]);
            return response()->json([
                'message' => 'Tutor profile updated successfully',
                'data'    => $row,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error updating tutor profile", ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Server error while updating tutor profile',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /** DELETE /api/tutors/{userId} */
    public function destroy($userId)
    {
        $user = Auth::user();
        Log::info("TutorController@destroy called", ['auth_user' => $user, 'target_user' => $userId]);

        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        if ($user->role !== 'admin' && $user->id != (int)$userId) {
            Log::warning("Forbidden delete attempt", ['auth_user' => $user->id, 'target_user' => $userId]);
            return response()->json(['message' => 'Forbidden'], 403);
        }

        try {
            $deleted = $this->tutorService->deleteByUserId((int)$userId);
            Log::info("Tutor profile deleted", ['target_user' => $userId, 'deleted' => $deleted]);
            return response()->json([
                'message' => $deleted ? 'Tutor profile deleted successfully' : 'Nothing to delete',
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error deleting tutor profile", ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Server error while deleting tutor profile',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
