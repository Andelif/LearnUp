<?php

namespace App\Http\Controllers;

use App\Services\ConfirmedTuitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConfirmedTuitionController extends Controller
{
    protected ConfirmedTuitionService $confirmedTuitionService;

    public function __construct(ConfirmedTuitionService $confirmedTuitionService)
    {
        $this->confirmedTuitionService = $confirmedTuitionService;
    }

    /** GET /api/confirmed-tuitions */
    public function index()
    {
        return response()->json($this->confirmedTuitionService->getAllConfirmedTuitions(), 200);
    }

    /** GET /api/confirmed-tuitions/{id} */
    public function show($id)
    {
        $row = $this->confirmedTuitionService->getConfirmedTuitionById((int)$id);
        if (!$row) return response()->json(['message' => 'Not found'], 404);
        return response()->json($row, 200);
    }

    /** POST /api/confirmed-tuitions */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_id'  => 'required|exists:applications,ApplicationID',
            'tution_id'       => 'required|exists:tuition_requests,TutionID',
            'FinalizedSalary' => 'required|numeric',
            'FinalizedDays'   => 'required|string',
            'Status'          => 'required|in:Ongoing,Ended',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $result = $this->confirmedTuitionService->storeConfirmedTuition($validator->validated());

        if (!empty($result['error'])) {
            return response()->json(['error' => $result['error']], 400);
        }
        return response()->json(['message' => $result['message'], 'data' => $result['data']], 201);
    }

    /** GET /api/confirmed-tuition/invoice/{tutionId} */
    public function getPaymentVoucher($tutionId)
    {
        $result = $this->confirmedTuitionService->getPaymentVoucher((int)$tutionId);
        if (!empty($result['error'])) {
            return response()->json(['error' => $result['error']], 403);
        }
        return response()->json($result, 200);
    }

    /** POST /api/payment-marked/{tutionId} */
    public function markPayment(Request $request, $tutionId)
    {
        $result = $this->confirmedTuitionService->markPayment((int)$tutionId);
        if (!empty($result['error'])) {
            return response()->json(['error' => $result['error']], 403);
        }
        return response()->json($result, 200);
    }

    /** DELETE /api/confirmed-tuitions/{id} */
    public function destroy($id)
    {
        $result = $this->confirmedTuitionService->deleteConfirmedTuition((int)$id);
        return response()->json($result, 200);
    }
}
