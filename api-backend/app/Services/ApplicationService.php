<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApplicationService
{
    /** Admin: list all applications; controller will scope for tutor/learner */
    public function getAllApplications()
    {
        return Application::orderByDesc('ApplicationID')->get();
    }

    public function getApplicationsByTutorUser(int $userId)
    {
        $tutorId = DB::table('tutors')->where('user_id', $userId)->value('TutorID');
        if (!$tutorId) return collect();
        return Application::where('tutor_id', $tutorId)->orderByDesc('ApplicationID')->get();
    }

    public function getApplicationsByLearnerUser(int $userId)
    {
        $learnerId = DB::table('learners')->where('user_id', $userId)->value('LearnerID');
        if (!$learnerId) return collect();
        return Application::where('learner_id', $learnerId)->orderByDesc('ApplicationID')->get();
    }

    public function getTutorStats(int $userId): ?array
    {
        $tutorId = DB::table('tutors')->where('user_id', $userId)->value('TutorID');
        if (!$tutorId) return null;

        return [
            'applied'     => Application::where('tutor_id', $tutorId)->where('status', 'Applied')->count(),
            'shortlisted' => Application::where('tutor_id', $tutorId)->where('status', 'Shortlisted')->count(),
            'confirmed'   => Application::where('tutor_id', $tutorId)->where('status', 'Confirmed')->count(),
            'cancelled'   => Application::where('tutor_id', $tutorId)->where('status', 'Cancelled')->count(),
        ];
    }

    public function getLearnerStats(int $userId): ?array
    {
        $learnerId = DB::table('learners')->where('user_id', $userId)->value('LearnerID');
        if (!$learnerId) return null;

        return [
            'applied'     => Application::where('learner_id', $learnerId)->where('status', 'Applied')->count(),
            'shortlisted' => Application::where('learner_id', $learnerId)->where('status', 'Shortlisted')->count(),
            'confirmed'   => Application::where('learner_id', $learnerId)->where('status', 'Confirmed')->count(),
            'cancelled'   => Application::where('learner_id', $learnerId)->where('status', 'Cancelled')->count(),
        ];
    }

    /** Tutor creates an application for a TutionID */
    public function createApplication(array $data): array
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'tutor') {
            return ['error' => 'Unauthorized: Only tutors can apply for tuition requests', 'status' => 403];
        }

        // Validate tution_id exists
        $tutionId = (int)($data['tution_id'] ?? 0);
        if (!$tutionId) {
            return ['error' => 'tution_id is required', 'status' => 400];
        }

        $existsTuition = DB::table('tuition_requests')->where('TutionID', $tutionId)->exists();
        if (!$existsTuition) {
            return ['error' => 'Invalid tuition request', 'status' => 400];
        }

        // Resolve tutor_id and learner_id
        $tutorId = DB::table('tutors')->where('user_id', $user->id)->value('TutorID');
        if (!$tutorId) {
            return ['error' => 'Tutor not found', 'status' => 404];
        }

        $learnerId = DB::table('tuition_requests')->where('TutionID', $tutionId)->value('LearnerID');
        if (!$learnerId) {
            return ['error' => 'Learner not found for this tuition request', 'status' => 404];
        }

        // Prevent duplicate application
        $already = Application::where('tution_id', $tutionId)->where('tutor_id', $tutorId)->exists();
        if ($already) {
            return ['error' => 'You have already applied for this job', 'status' => 400];
        }

        $row = DB::transaction(function () use ($tutionId, $learnerId, $tutorId) {
            return Application::create([
                'tution_id'  => $tutionId,
                'learner_id' => $learnerId,
                'tutor_id'   => $tutorId,
                'matched'    => false,
                'status'     => 'Applied',
            ]);
        });

        return ['message' => 'Application submitted successfully', 'data' => $row, 'status' => 201];
    }
}
