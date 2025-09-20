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
        Log::info("LearnerController@index", ['user' => $user]);

        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        if ($user->role === 'admin') {
            Log::info("Admin fetching all learners");
            return response()->json($this->learnerService->getAllLearners(), 200);
        }

        if ($user->role !== 'learner') {
            Log::warning("Forbidden learner index access", ['role' => $user->role]);
            return response()->json(['message' => 'Forbidden: not a learner'], 403);
        }

        $me = $this->learnerService->getLearnerByUserId($user->id);
        Log::info("Learner profile fetched", ['id' => $user->id, 'profile' => $me]);
        return response()->json($me, 200);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        Log::info("LearnerController@store", ['user' => $user, 'input' => $request->all()]);

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
            Log::warning("Learner store validation failed", ['errors' => $validator->errors()]);
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
            Log::info("Learner profile picture uploaded", ['path' => $validated['profile_picture']]);
        }

        $row = $this->learnerService->upsertForUser($user->id, $validated);

        return response()->json(['message' => 'Learner profile saved successfully', 'data' => $row], 201);
    }

    public function show($userId)
    {
        $user = Auth::user();
        Log::info("LearnerController@show", ['auth_user' => $user, 'target_user' => $userId]);

        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        if ($user->role !== 'admin' && $user->id != (int)$userId) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $row = $this->learnerService->getLearnerByUserId((int)$userId);
        if (!$row) return response()->json(['message' => 'Not found'], 404);

        return response()->json($row, 200);
    }

    public function update(Request $request, $userId)
    {
        $user = Auth::user();
        Log::info("LearnerController@update", ['auth_user' => $user, 'target_user' => $userId, 'input' => $request->all()]);

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
            Log::warning("Learner update validation failed", ['errors' => $validator->errors()]);
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
            Log::info("Learner profile picture updated", ['path' => $validated['profile_picture']]);
        }

        $row = $this->learnerService->updateForUser((int)$userId, $validated);

        return response()->json(['message' => 'Learner profile updated successfully', 'data' => $row], 200);
    }

    public function destroy($userId)
    {
        $user = Auth::user();
        Log::info("LearnerController@destroy", ['auth_user' => $user, 'target_user' => $userId]);

        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        if ($user->role !== 'admin' && $user->id != (int)$userId) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $deleted = $this->learnerService->deleteByUserId((int)$userId);
        return response()->json(['message' => $deleted ? 'Learner profile deleted' : 'Nothing to delete'], 200);
    }
}
