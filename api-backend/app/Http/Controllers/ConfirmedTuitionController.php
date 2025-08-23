<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ConfirmedTuitionService;

class ConfirmedTuitionController extends Controller
{
    protected $confirmedTuitionService;

    public function __construct(ConfirmedTuitionService $confirmedTuitionService)
    {
        $this->confirmedTuitionService = $confirmedTuitionService;
    }

    public function index()
    {
        $confirmedTuitions = $this->confirmedTuitionService->getAllConfirmedTuitions();
        return response()->json($confirmedTuitions);
    }

    public function show($id)
    {
        $confirmedTuition = $this->confirmedTuitionService->getConfirmedTuitionById($id);
        return response()->json($confirmedTuition);
    }

    public function store(Request $request)
    {
        $response = $this->confirmedTuitionService->storeConfirmedTuition($request);
        if (isset($response['error'])) {
            return response()->json($response, 400);
        }

        return response()->json($response, 201);
    }

    public function getPaymentVoucher($tutionId)
    {
        $response = $this->confirmedTuitionService->getPaymentVoucher($tutionId);
        if (isset($response['error'])) {
            return response()->json($response, 403);
        }

        return response()->json($response, 200);
    }

    public function markPayment(Request $request, $tutionId)
    {
        $response = $this->confirmedTuitionService->markPayment($tutionId);
        if (isset($response['error'])) {
            return response()->json($response, 403);
        }

        return response()->json($response, 200);
    }

    public function destroy($id)
    {
        $response = $this->confirmedTuitionService->deleteConfirmedTuition($id);
        return response()->json($response, 200);
    }
}
