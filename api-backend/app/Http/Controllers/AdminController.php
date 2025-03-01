<?php

namespace App\Http\Controllers;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }


    
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($this->adminService->getAllAdmins());
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:15',
            'permission_req' => 'nullable|boolean',
        ]);

        $response = $this->adminService->createAdmin($request->all(), Auth::id());

        return response()->json($response, isset($response['error']) ? 400 : 201);
    }

    public function show($id)
    {
        $admin = $this->adminService->getAdminById($id);

        if (!$admin || (Auth::id() !== $admin[0]->user_id && Auth::user()->role !== 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($admin[0]);
    }

    public function update(Request $request, $id)
    {
        if (Auth::id() !== $id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->adminService->updateAdmin($id, $request->all());

        return response()->json(['message' => 'Admin updated successfully']);
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($this->adminService->deleteAdmin($id));
    }


    public function getLearners()
    {
        return response()->json($this->adminService->getLearners());
    }

    public function getTutors()
    {
        return response()->json($this->adminService->getTutors());
    }

    public function getTuitionRequests()
    {
        return response()->json($this->adminService->getTuitionRequests());
    }

    public function getApplications()
    {
        return response()->json($this->adminService->getApplications());
    }


    public function getApplicationsByTuitionID($tutionID)
    {
        return response()->json($this->adminService->getApplicationsByTuitionID($tutionID));
    }

    public function matchTutor(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,ApplicationID',
        ]);

        $response = $this->adminService->matchTutor($request->application_id);

        return response()->json($response, isset($response['error']) ? 404 : 200);
    }




}
