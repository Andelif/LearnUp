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

    /** GET /api/tutors */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            \Log::error("Tutor index: Unauthorized access attempt");
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        \Log::info("Tutor index: User {$user->id} with role {$user->role}");

        if ($user->role === 'admin') {
            $all = $this->tutorService->getAllTutors();
            \Log::info("Tutor index: Returning all tutors (" . count($all) . ")");
            return response()->json($all, 200);
        }

        if ($user->role !== 'tutor') {
            \Log::warning("Tutor index: Forbidden for role {$user->role}");
            return response()->json(['message' => 'Forbidden: not a tutor'], 403);
        }

        $me = $this->tutorService->getTutorByUserId($user->id);
        \Log::info("Tutor index: Returning tutor profile for user_id {$user->id}");
        return response()->json($me, 200);
    }

    /** POST /api/tutors */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'tutor') {
            \Log::warning("Tutor store: Forbidden for user");
            return response()->json(['message' => 'Forbidden: not a tutor'], 403);
        }

        $validator = Validator::make($request->all(), [
            'full_name'              => 'required|string|max:255',
            'address'                => 'nullable|string|max:255',
            'contact_number'         => 'nullable|string|max:20',
            'gender'                 => 'nullable|string|in:male,female,other,Male,Female,Other',
            'preferred_salary'       => 'nullable|integer|min:0',
            'qualification'          => 'nullable|string|max:255',
            'experience'             => 'nullable|string|max:255',
            'currently_studying_in'  => 'nullable|string|max:255',
            'preferred_location'     => 'nullable|string|max:255',
            'preferred_time'         => 'nullable|string|max:255',
            'availability'           => 'nullable|boolean',
            'profile_picture'        => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            \Log::error("Tutor store: Validation failed", $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validated = $validator->validated();
        \Log::info("Tutor store: Validated data", $validated);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = "/storage/" . $path;
            \Log::info("Tutor store: Stored profile picture at {$validated['profile_picture']}");
        }

        $row = $this->tutorService->upsertForUser($user->id, $validated);

        \Log::info("Tutor store: Profile saved successfully for user_id {$user->id}");
        return response()->json([
            'message' => 'Tutor profile saved successfully',
            'data'    => $row,
        ], 201);
    }

    /** GET /api/tutors/{userId} */
    public function show($userId)
    {
        $user = Auth::user();
        if (!$user) {
            \Log::error("Tutor show: Unauthorized");
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($user->role !== 'admin' && $user->id != (int)$userId) {
            \Log::warning("Tutor show: Forbidden. User {$user->id} tried to access {$userId}");
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $row = $this->tutorService->getTutorByUserId((int)$userId);
        if (!$row) {
            \Log::warning("Tutor show: Not found for user_id {$userId}");
            return response()->json(['message' => 'Not found'], 404);
        }

        \Log::info("Tutor show: Returning profile for user_id {$userId}");
        return response()->json($row, 200);
    }

    /** PUT /api/tutors/{userId} */
    public function update(Request $request, $userId)
    {
        $user = Auth::user();
        if (!$user) {
            \Log::error("Tutor update: Unauthorized");
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'full_name'              => 'sometimes|required|string|max:255',
            'address'                => 'sometimes|nullable|string|max:255',
            'contact_number'         => 'sometimes|nullable|string|max:20',
            'gender'                 => 'sometimes|nullable|string|in:male,female,other,Male,Female,Other',
            'preferred_salary'       => 'sometimes|nullable|integer|min:0',
            'qualification'          => 'sometimes|nullable|string|max:255',
            'experience'             => 'sometimes|nullable|string|max:255',
            'currently_studying_in'  => 'sometimes|nullable|string|max:255',
            'preferred_location'     => 'sometimes|nullable|string|max:255',
            'preferred_time'         => 'sometimes|nullable|string|max:255',
            'availability'           => 'sometimes|nullable|boolean',
            'profile_picture'        => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            \Log::error("Tutor update: Validation failed", $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validated = $validator->validated();
        \Log::info("Tutor update: Validated data", $validated);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = "/storage/" . $path;
            \Log::info("Tutor update: Stored new profile picture at {$validated['profile_picture']}");
        }

        $row = $this->tutorService->upsertForUser((int)$userId, $validated);

        \Log::info("Tutor update: Profile updated successfully for user_id {$userId}");
        return response()->json([
            'message' => 'Tutor profile updated successfully',
            'data'    => $row,
        ], 200);
    }

    /** DELETE /api/tutors/{userId} */
    public function destroy($userId)
    {
        $user = Auth::user();
        if (!$user) {
            \Log::error("Tutor destroy: Unauthorized");
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($user->role !== 'admin' && $user->id != (int)$userId) {
            \Log::warning("Tutor destroy: Forbidden. User {$user->id} tried to delete {$userId}");
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $deleted = $this->tutorService->deleteByUserId((int)$userId);
        \Log::info("Tutor destroy: Delete result for user_id {$userId} = {$deleted}");

        return response()->json([
            'message' => $deleted ? 'Tutor profile deleted successfully' : 'Nothing to delete',
        ], 200);
    }
}
