<?php

namespace App\Http\Controllers;

use App\Models\PaymentVoucher;
use App\Services\MediaFeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $mediaFeeService;

    public function __construct(MediaFeeService $mediaFeeService)
    {
        $this->mediaFeeService = $mediaFeeService;
    }

    /**
     * Create a new payment voucher
     */
    public function createPaymentVoucher(Request $request)
    {
        $request->validate([
            'tution_id' => 'required|exists:confirmed_tuitions,tution_id',
            'tutor_id' => 'required|exists:tutors,TutorID',
            'learner_id' => 'required|exists:learners,LearnerID',
            'total_salary' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            // calculate fees using service
            $fees = $this->mediaFeeService->calculateFee((float) $request->total_salary);

            $voucher = PaymentVoucher::create([
                'tution_id' => $request->tution_id,
                'tutor_id' => $request->tutor_id,
                'learner_id' => $request->learner_id,
                'total_salary' => $request->total_salary,
                'media_fee_amount' => $fees['media_fee_amount'],
                'tutor_amount' => $fees['tutor_amount'],
                'voucher_id' => $this->mediaFeeService->generateUniqueVoucherId(),
                'due_date' => $request->due_date,
                'status' => 'Pending',
            ]);

            DB::commit();
            return response()->json(['success' => true, 'voucher' => $voucher], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get payment voucher by tutionId
     */
    public function getPaymentVoucher($tutionId)
    {
        $voucher = PaymentVoucher::with(['tution', 'tutor', 'learner'])
            ->where('tution_id', $tutionId)
            ->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Voucher not found'], 404);
        }

        return response()->json(['success' => true, 'voucher' => $voucher], 200);
    }

    /**
     * Mark payment as completed
     */
    public function markPaymentCompleted(Request $request, $tutionId)
    {
        $voucher = PaymentVoucher::where('tution_id', $tutionId)->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Voucher not found'], 404);
        }

        $voucher->update([
            'status' => 'Completed',
            'payment_method' => $request->payment_method ?? 'Other',
            'transaction_id' => $request->transaction_id ?? null,
            'paid_at' => now(),
            'payment_notes' => $request->payment_notes ?? null,
        ]);

        return response()->json(['success' => true, 'voucher' => $voucher], 200);
    }
}
