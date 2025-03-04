<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TuitionRequestService;


class TuitionRequestController extends Controller
{
    protected $tuitionRequestService;

    public function __construct(TuitionRequestService $tuitionRequestService)
    {
        $this->tuitionRequestService = $tuitionRequestService;
    }
    public function index()
    {
        $tuitionRequests = $this->tuitionRequestService->getAllTuitionRequests();
        return response()->json($tuitionRequests);
    }
    public function getAllRequests()
   {
    return response()->json($this->tuitionRequestService->getUserTuitionRequests());
   
    }

    public function show($id)
    {
        $tuitionRequest = $this->tuitionRequestService->getTuitionRequestById($id);

        if (!$tuitionRequest) {
            return response()->json(['message' => 'Tuition request not found'], 404);
        }

        return response()->json($tuitionRequest);
    }

    public function store(Request $request)
    { 
        $result = $this->tuitionRequestService->createTuitionRequest($request);

        return response()->json($result, 201);
       

    }
    public function filterTuitionRequests(Request $request)
    {
        
        $filters = $request->only(['class', 'subjects', 'asked_salary_min', 'asked_salary_max', 'location']);
        
        
        $filteredRequests = TuitionRequest::filterTuitionRequests($filters);

        return response()->json($filteredRequests);
    }
    public function update(Request $request, $id)
    {
        $result = $this->tuitionRequestService->updateTuitionRequest($request, $id);

        if (!$result) {
            return response()->json(['message' => 'Tuition request not found'], 404);
        }

        return response()->json($result);
    }
    public function destroy($id)
    {
        $result = $this->tuitionRequestService->deleteTuitionRequest($id);

        if (!$result) {
            return response()->json(['message' => 'Tuition request not found'], 404);
        }

        return response()->json($result);
    }
}