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

    /**
     * GET /api/learners
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($user->role === 'admin') {
            return response()->json($this->learnerService->getAllLearners(), 200);
        }

        if ($user->role !== 'learner') {
            return response()->json(['message' => 'Forbidden: not a learner'], 403);
        }

        $me = $this->learnerService->getLearnerByUserId($user->id);
        return response()->json($me, 200);
    }

    /**
     * POST /api/learners
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'learner') {
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
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validated = $validator->validated();

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = "/storage/" . $path;
        }

        $row = $this->learnerService->upsertForUser($user->id, $validated);

        return response()->json([
            'message' => 'Learner profile saved',
            'data'    => $row,
        ], 201);
    }

    /**
     * GET /api/learners/{userId}
     */
    public function show($userId)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        if ($user->role !== 'admin' && $user->id != (int)$userId) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $row = $this->learnerService->getLearnerByUserId((int)$userId);
        if (!$row) return response()->json(['message' => 'Not found'], 404);

        return response()->json($row, 200);
    }

    /**
     * PUT /api/learners/{userId}
     */
    public function update(Request $request, $userId)
    {
        $user = Auth::user();
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
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validated = $validator->validated();

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = "/storage/" . $path;
        }

        $row = $this->learnerService->updateForUser((int)$userId, $validated);

        return response()->json([
            'message' => 'Learner profile updated successfully',
            'data'    => $row,
        ], 200);
    }

    /**
     * DELETE /api/learners/{userId}
     */
    public function destroy($userId)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        if ($user->role !== 'admin' && $user->id != (int)$userId) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $deleted = $this->learnerService->deleteByUserId((int)$userId);
        return response()->json(['message' => $deleted ? 'Learner profile deleted' : 'Nothing to delete'], 200);
    }
}
