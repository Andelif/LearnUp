<?php

namespace App\Http\Controllers;
use App\Services\LearnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LearnerController extends Controller
{
    protected $learnerService;

    
    public function __construct(LearnerService $learnerService)
    {
        $this->learnerService = $learnerService;
    }
    
    public function index()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'learner') {
            return response()->json(['message' => 'Unauthorized: You are not a learner'], 403);
        }
        
        $learners = $this->learnerService->getAllLearners();
        return response()->json($learners);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'learner') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $this->learnerService->createOrUpdateLearner($request);
        return response()->json(['message' => 'Learner profile updated successfully'], 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $learner = $this->learnerService->getLearnerById($id);
        
        if (!$learner || $user->id !== $learner->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        return response()->json($learner);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $learner = $this->learnerService->getLearnerById($id);
        
        if (!$learner || $user->id !== $learner->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $this->learnerService->updateLearner($request, $id);
        return response()->json(['message' => 'Learner profile updated successfully']);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $learner = $this->learnerService->getLearnerById($id);
        
        if (!$learner || $user->id !== $learner->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $this->learnerService->deleteLearner($id);
        return response()->json(['message' => 'Learner profile deleted successfully']);
    }
}