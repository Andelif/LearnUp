<?php

namespace App\Http\Controllers;

use App\Services\TuitionRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TuitionRequestController extends Controller
{
    protected TuitionRequestService $tuitionRequestService;

    public function __construct(TuitionRequestService $tuitionRequestService)
    {
        $this->tuitionRequestService = $tuitionRequestService;
    }

    /** GET /api/tuition-requests/all  (public list) */
    public function index()
    {
        return response()->json($this->tuitionRequestService->getAllTuitionRequests(), 200);
    }

    /** GET /api/tuition-requests  (current learner’s list; in auth group) */
    public function getAllRequests()
    {
        return response()->json($this->tuitionRequestService->getUserTuitionRequests(), 200);
    }

    /** GET /api/tuition-requests/{id} */
    public function show($id)
    {
        $row = $this->tuitionRequestService->getTuitionRequestById((int)$id);
        if (!$row) return response()->json(['message' => 'Tuition request not found'], 404);
        return response()->json($row, 200);
    }

    /** POST /api/tuition-requests  (auth: learner) */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class'        => 'required|string|max:255',
            'subjects'     => 'required|string',
            'asked_salary' => 'required|numeric',
            'curriculum'   => 'required|string|max:255',
            'days'         => 'required|string|max:255',
            'location'     => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $result = $this->tuitionRequestService->create($validator->validated());

        if (!empty($result['error'])) {
            return response()->json(['error' => $result['error']], $result['status'] ?? 400);
        }
        return response()->json(['message' => $result['message'], 'data' => $result['data']], $result['status'] ?? 201);
    }

    /** GET /api/tuition-requests/filter  (public) */
    public function filterTuitionRequests(Request $request)
    {
        $filters = $request->only(['class', 'subjects', 'asked_salary_min', 'asked_salary_max', 'location']);
        $rows = $this->tuitionRequestService->filter($filters);
        return response()->json($rows, 200);
    }

    /** PUT /api/tuition-requests/{id}  (owner or admin) */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'class'        => 'sometimes|required|string|max:255',
            'subjects'     => 'sometimes|required|string',
            'asked_salary' => 'sometimes|required|numeric',
            'curriculum'   => 'sometimes|required|string|max:255',
            'days'         => 'sometimes|required|string|max:255',
            'location'     => 'sometimes|required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $result = $this->tuitionRequestService->update((int)$id, $validator->validated());

        if (!empty($result['error'])) {
            return response()->json(['error' => $result['error']], $result['status'] ?? 400);
        }
        return response()->json(['message' => $result['message'], 'data' => $result['data']], $result['status'] ?? 200);
    }

    /** DELETE /api/tuition-requests/{id}  (owner or admin) */
    public function destroy($id)
    {
        $result = $this->tuitionRequestService->delete((int)$id);

        if (!empty($result['error'])) {
            return response()->json(['error' => $result['error']], $result['status'] ?? 400);
        }
        return response()->json(['message' => $result['message']], $result['status'] ?? 200);
    }
}
