<?php

namespace App\Http\Controllers;

use App\Services\LearnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LearnerController extends Controller
{
    protected LearnerService $learnerService;

    public function __construct(LearnerService $learnerService)
    {
        $this->learnerService = $learnerService;
    }

    /** GET /api/learners */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            \Log::error("Learner index: Unauthorized access attempt");
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        \Log::info("Learner index: User {$user->id} with role {$user->role}");

        if ($user->role === 'admin') {
            $all = $this->learnerService->getAllLearners();
            \Log::info("Learner index: Returning all learners (" . count($all) . ")");
            return response()->json($all, 200);
        }

        if ($user->role !== 'learner') {
            \Log::warning("Learner index: Forbidden for role {$user->role}");
            return response()->json(['message' => 'Forbidden: not a learner'], 403);
        }

        $me = $this->learnerService->getLearnerByUserId($user->id);
        \Log::info("Learner index: Returning learner profile for user_id {$user->id}");
        return response()->json($me, 200);
    }

    /** POST /api/learners */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'learner') {
            \Log::warning("Learner store: Forbidden for user");
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
            \Log::error("Learner store: Validation failed", $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validated = $validator->validated();
        \Log::info("Learner store: Validated data", $validated);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = "/storage/" . $path;
            \Log::info("Learner store: Stored profile picture at {$validated['profile_picture']}");
        }

        $row = $this->learnerService->upsertForUser($user->id, $validated);

        \Log::info("Learner store: Profile saved successfully for user_id {$user->id}");
        return response()->json([
            'message' => 'Learner profile saved successfully',
            'data'    => $row,
        ], 201);
    }

    /** GET /api/learners/{userId} */
    public function show($userId)
    {
        $user = Auth::user();
        if (!$user) {
            \Log::error("Learner show: Unauthorized");
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($user->role !== 'admin' && $user->id != (int)$userId) {
            \Log::warning("Learner show: Forbidden access. User {$user->id} tried to access {$userId}");
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $row = $this->learnerService->getLearnerByUserId((int)$userId);
        if (!$row) {
            \Log::warning("Learner show: Not found for user_id {$userId}");
            return response()->json(['message' => 'Not found'], 404);
        }

        \Log::info("Learner show: Returning profile for user_id {$userId}");
        return response()->json($row, 200);
    }

    /** PUT /api/learners/{userId} */
    public function update(Request $request, $userId)
    {
        $user = Auth::user();
        if (!$user) {
            \Log::error("Learner update: Unauthorized");
            return response()->json(['message' => 'Unauthorized'], 401);
        }

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
            \Log::error("Learner update: Validation failed", $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validated = $validator->validated();
        \Log::info("Learner update: Validated data", $validated);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = "/storage/" . $path;
            \Log::info("Learner update: Stored new profile picture at {$validated['profile_picture']}");
        }

        $row = $this->learnerService->updateForUser((int)$userId, $validated);

        \Log::info("Learner update: Profile updated successfully for user_id {$userId}");
        return response()->json([
            'message' => 'Learner profile updated successfully',
            'data'    => $row,
        ], 200);
    }

    /** DELETE /api/learners/{userId} */
    public function destroy($userId)
    {
        $user = Auth::user();
        if (!$user) {
            \Log::error("Learner destroy: Unauthorized");
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($user->role !== 'admin' && $user->id != (int)$userId) {
            \Log::warning("Learner destroy: Forbidden. User {$user->id} tried to delete {$userId}");
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $deleted = $this->learnerService->deleteByUserId((int)$userId);
        \Log::info("Learner destroy: Delete result for user_id {$userId} = {$deleted}");

        return response()->json(['message' => $deleted ? 'Learner profile deleted successfully' : 'Nothing to delete'], 200);
    }
}
