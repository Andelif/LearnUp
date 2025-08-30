<?php

namespace App\Http\Controllers;

use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    protected AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    private function ensureAdmin()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden: not an admin'], 403);
        }
        return null;
    }

    /** GET /api/admins (admin only) */
    public function index()
    {
        if ($resp = $this->ensureAdmin()) return $resp;
        return response()->json($this->adminService->getAllAdmins(), 200);
    }

    /** POST /api/admins (admin only, creates/updates own admin profile) */
    public function store(Request $request)
    {
        if ($resp = $this->ensureAdmin()) return $resp;

        $validator = Validator::make($request->all(), [
            'full_name'      => 'required|string|max:255',
            'address'        => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'permission_req' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $response = $this->adminService->createAdmin($validator->validated(), Auth::id());
        return response()->json($response, isset($response['error']) ? 400 : 201);
    }

    /** GET /api/admins/{id} (admin only, or owner of that admin record) */
    public function show($id)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $admin = $this->adminService->getAdminById((int)$id);
        if (!$admin) return response()->json(['message' => 'Not found'], 404);

        if ($user->role !== 'admin' && $user->id !== $admin->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json($admin, 200);
    }

    /** PUT /api/admins/{id} (admin only) */
    public function update(Request $request, $id)
    {
        if ($resp = $this->ensureAdmin()) return $resp;

        $validator = Validator::make($request->all(), [
            'full_name'      => 'sometimes|required|string|max:255',
            'address'        => 'sometimes|nullable|string|max:255',
            'contact_number' => 'sometimes|nullable|string|max:20',
            'permission_req' => 'sometimes|nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $response = $this->adminService->updateAdmin((int)$id, $validator->validated());

        return response()->json($response, isset($response['error']) ? 404 : 200);
    }

    /** DELETE /api/admins/{id} (admin only) */
    public function destroy($id)
    {
        if ($resp = $this->ensureAdmin()) return $resp;
        return response()->json($this->adminService->deleteAdmin((int)$id), 200);
    }

    /** GET /api/admin/learners (admin only) */
    public function getLearners()
    {
        if ($resp = $this->ensureAdmin()) return $resp;
        return response()->json($this->adminService->getLearners(), 200);
    }

    /** DELETE /api/admin/learners/{learnerId} (admin only) */
    public function deleteLearner($learnerId)
    {
        if ($resp = $this->ensureAdmin()) return $resp;

        $result = $this->adminService->deleteLearner((int)$learnerId);
        if ($result['status'] === 'error') {
            return response()->json(['error' => $result['message']], 404);
        }
        return response()->json(['message' => $result['message']], 200);
    }

    /** GET /api/admin/tutors (admin only) */
    public function getTutors()
    {
        if ($resp = $this->ensureAdmin()) return $resp;
        return response()->json($this->adminService->getTutors(), 200);
    }

    /** DELETE /api/admin/tutors/{tutorId} (admin only) */
    public function deleteTutor($tutorId)
    {
        if ($resp = $this->ensureAdmin()) return $resp;

        $result = $this->adminService->deleteTutor((int)$tutorId);
        if ($result['status'] === 'error') {
            return response()->json(['error' => $result['message']], 404);
        }
        return response()->json(['message' => $result['message']], 200);
    }

    /** GET /api/admin/tuition-requests (admin only) */
    public function getTuitionRequests()
    {
        if ($resp = $this->ensureAdmin()) return $resp;
        return response()->json($this->adminService->getTuitionRequests(), 200);
    }

    /** GET /api/admin/applications (admin only) */
    public function getApplications()
    {
        if ($resp = $this->ensureAdmin()) return $resp;
        return response()->json($this->adminService->getApplications(), 200);
    }

    /** GET /api/admin/applications/{tuitionId} (admin only) */
    public function getApplicationsByTuitionID($tuitionId)
    {
        if ($resp = $this->ensureAdmin()) return $resp;
        return response()->json($this->adminService->getApplicationsByTuitionID((int)$tuitionId), 200);
    }

    /** POST /api/admin/match-tutor (admin only) */
    public function matchTutor(Request $request)
    {
        if ($resp = $this->ensureAdmin()) return $resp;

        $validator = Validator::make($request->all(), [
            'application_id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $response = $this->adminService->matchTutor((int)$request->application_id);

        return response()->json($response, isset($response['error']) ? 404 : 200);
    }
}
