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

    public function index()
    {
        $user = Auth::user();
        Log::info("TutorController@index", ['user' => $user]);

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

    public function store(Request $request)
    {
        $user = Auth::user();
        Log::info("TutorController@store", ['user' => $user, 'input' => $request->all()]);

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
            Log::warning("Tutor store validation failed", ['errors' => $validator->errors()]);
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        $validated = $validator->validated();

        Log::info("File check", [
            'hasFile' => $request->hasFile('profile_picture'),
            'files'   => $request->allFiles(),
        ]);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = "/storage/" . $path;
            Log::info("Tutor profile picture uploaded", ['path' => $validated['profile_picture']]);
        }

        $row = $this->tutorService->upsertForUser($user->id, $validated);

        return response()->json(['message' => 'Tutor profile saved successfully', 'data' => $row], 201);
    }

    public function update(Request $request, $userId)
    {
        $user = Auth::user();
        Log::info("TutorController@update", ['auth_user' => $user, 'target_user' => $userId, 'input' => $request->all()]);

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
            Log::warning("Tutor update validation failed", ['errors' => $validator->errors()]);
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        $validated = $validator->validated();

        Log::info("File check", [
            'hasFile' => $request->hasFile('profile_picture'),
            'files'   => $request->allFiles(),
        ]);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = "/storage/" . $path;
            Log::info("Tutor profile picture updated", ['path' => $validated['profile_picture']]);
        }

        $row = $this->tutorService->upsertForUser((int)$userId, $validated);

        return response()->json(['message' => 'Tutor profile updated successfully', 'data' => $row], 200);
    }

    public function destroy($userId)
    {
        $user = Auth::user();
        Log::info("TutorController@destroy", ['auth_user' => $user, 'target_user' => $userId]);

        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        if ($user->role !== 'admin' && $user->id != (int)$userId) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $deleted = $this->tutorService->deleteByUserId((int)$userId);
        return response()->json(['message' => $deleted ? 'Tutor profile deleted successfully' : 'Nothing to delete'], 200);
    }
}
