<?php

namespace App\Http\Controllers;
use App\Services\TutorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TutorController extends Controller
{
    protected $tutorService;

    
    public function __construct(TutorService $tutorService)
    {
        $this->tutorService = $tutorService;
    }

    public function index()
    {
        if (Auth::user()->role !== 'tutor') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $tutors = $this->tutorService->getAllTutors();
        return response()->json($tutors);
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'tutor') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->tutorService->updateOrCreateTutorProfile($request);

        return response()->json(['message' => 'Tutor profile updated successfully'], 201);
    }

    public function show($id)
    {
        $tutor = $this->tutorService->getTutorById($id);

        if (!$tutor || (Auth::id() !== $tutor[0]->user_id && Auth::user()->role !== 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($tutor[0]);
    }
   

    public function update(Request $request, $id)
    {
        $tutor = $this->tutorService->getTutorById($id);

        if (!$tutor || Auth::id() !== $tutor[0]->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->tutorService->updateTutorProfile($request, $id);

        return response()->json(['message' => 'Tutor profile updated successfully']);
    }

    public function destroy($id)
    {
        $tutor = $this->tutorService->getTutorById($id);

        if (!$tutor || (Auth::id() !== $tutor[0]->user_id && Auth::user()->role !== 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->tutorService->deleteTutor($id);

        return response()->json(['message' => 'Tutor profile deleted successfully']);
    }
}