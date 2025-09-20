<?php

namespace App\Http\Controllers;

use App\Services\LearnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class LearnerController extends Controller
{
    protected LearnerService $learnerService;

    public function __construct(LearnerService $learnerService)
    {
        $this->learnerService = $learnerService;
    }

    public function index()
    {
        $user = Auth::user();
        Log::info("LearnerController@index called", ['user' => $user]);

        if (!$user) {
            Log::warning("Unauthorized access to learners index");
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($user->role === 'admin') {
            Log::info("Admin fetching all learners");
            return response()->json($this->learnerService->getAllLearners(), 200);
        }

        if ($user->role !== 'learner') {
            Log::warning("Forbidden: user tried to fetch learner index", ['role' => $user->role]);
            return response()->json(['message' => 'Forbidden: not a learner'], 403);
        }

        $me = $this->learnerService->getLearnerByUserId($user->id);
        Log::info("Learner profile fetched", ['user_id' => $user->id, 'profile' => $me]);
        return response()->json($me, 200);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        Log::info("LearnerController@store called", ['user' => $user, 'input' => $request->all()]);

        if (!$user || $user->role !== 'learner') {
            Log::warning("Forbidden store attempt", ['user' => $user]);
            return response()->json(['message' => 'Forbidden: not a learner'], 403);
        }

        $validator = Validator::make($request->all(), [
            'full_name'               => 'required|string|max:255',
            'guardian_full_name'      => 'nullable|string|max:255',
            'contact_number'          => 'nullable|string|max:20',
            'guardian_contact_number' => 'nullable|string|max:20',
            'gender'                  => 'nullable|string|in:male,female,other,Male,Female,Other',
            'address'                 => 'nullable|string|max:255',
            'profile_picture'         => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            Log::warning("Validation failed for learner store", ['errors' => $validator->errors()]);
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
                Log::info("Profile picture uploaded for learner", ['path' => $validated['profile_picture']]);
            } else {
                Log::info("No profile picture uploaded in learner store request");
            }

            $row = $this->learnerService->upsertForUser($user->id, $validated);

            Log::info("Learner profile saved successfully", ['user_id' => $user->id, 'profile' => $row]);
            return response()->json([
                'message' => 'Learner profile saved successfully',
                'data'    => $row,
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error saving learner profile", ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Server error while saving learner profile',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function show($userId)
    {
        $user = Auth::user();
        Log::info("LearnerController@show called", ['auth_user' => $user, 'target_user' => $userId]);

        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        if ($user->role !== 'admin' && $user->id != (int)$userId) {
            Log::warning("Forbidden show attempt", ['auth_user' => $user->id, 'target_user' => $userId]);
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $row = $this->learnerService->getLearnerByUserId((int)$userId);
        if (!$row) {
            Log::warning("Learner profile not found", ['target_user' => $userId]);
            return response()->json(['message' => 'Not found'], 404);
        }

        Log::info("Learner profile fetched via show", ['target_user' => $userId, 'profile' => $row]);
        return response()->json($row, 200);
    }

    public function update(Request $request, $userId)
    {
        $user = Auth::user();
        Log::info("LearnerController@update called", ['auth_user' => $user, 'target_user' => $userId, 'input' => $request->all()]);

        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $validator = Validator::make($request->all(), [
            'full_name'               => 'sometimes|required|string|max:255',
            'guardian_full_name'      => 'sometimes|nullable|string|max:255',
            'contact_number'          => 'sometimes|nullable|string|max:20',
            'guardian_contact_number' => 'sometimes|nullable|string|max:20',
            'gender'                  => 'sometimes|nullable|string|in:male,female,other,Male,Female,Other',
            'address'                 => 'sometimes|nullable|string|max:255',
            'profile_picture'         => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            Log::warning("Validation failed for learner update", ['errors' => $validator->errors()]);
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
                Log::info("Profile picture updated for learner", ['path' => $validated['profile_picture']]);
            }

            $row = $this->learnerService->updateForUser((int)$userId, $validated);

            Log::info("Learner profile updated successfully", ['user_id' => $userId, 'profile' => $row]);
            return response()->json([
                'message' => 'Learner profile updated successfully',
                'data'    => $row,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error updating learner profile", ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Server error while updating learner profile',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($userId)
    {
        $user = Auth::user();
        Log::info("LearnerController@destroy called", ['auth_user' => $user, 'target_user' => $userId]);

        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        if ($user->role !== 'admin' && $user->id != (int)$userId) {
            Log::warning("Forbidden delete attempt", ['auth_user' => $user->id, 'target_user' => $userId]);
            return response()->json(['message' => 'Forbidden'], 403);
        }

        try {
            $deleted = $this->learnerService->deleteByUserId((int)$userId);
            Log::info("Learner profile deleted", ['target_user' => $userId, 'deleted' => $deleted]);
            return response()->json(['message' => $deleted ? 'Learner profile deleted' : 'Nothing to delete'], 200);
        } catch (\Exception $e) {
            Log::error("Error deleting learner profile", ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Server error while deleting learner profile',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
