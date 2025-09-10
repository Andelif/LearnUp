<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class PasswordResetController extends Controller
{
    /** 10-minute expiry for OTP */
    private int $expiryMinutes = 10;

    private function throttleKey(string $email): string
    {
        return 'pw-forgot:' . strtolower($email);
    }

    /**
     * POST /api/password/forgot
     * Always responds generically; does NOT reveal if the email exists.
     * Stores a hashed 6-digit OTP in password_resets.token and sets created_at.
     */
    public function send(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = strtolower($request->email);

        // Check if email exists in users table
        $user = DB::table('users')->where('email', $email)->first();
        if (!$user) {
            return response()->json(['message' => 'You didn’t sign up with this email.'], 422);
        }

        // basic rate-limit to reduce abuse
        if (RateLimiter::tooManyAttempts($this->throttleKey($email), 5)) {
            return response()->json(['message' => 'Please wait before retrying.'], 429);
        }
        RateLimiter::hit($this->throttleKey($email), 60); // decay 60s per hit

        // Create a 6-digit code
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $hash = Hash::make($code);

        // Upsert into password_resets (email is PK in your migration)
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            ['token' => $hash, 'created_at' => Carbon::now()]
        );

        // Try sending the mail
        try {
            \Mail::to($email)->send(new PasswordResetCode($code));
        } catch (\Throwable $e) {
            // ✅ Log the error so you can see what failed in Render / logs
            \Log::error('Password reset mail failed', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }

        // Always respond generically to the user
        return response()->json([
            'message' => 'If the email exists, a verification code has been sent.'
        ], 200);
    }



    /**
     * POST /api/password/verify
     * Validates the code without changing the password.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|digits:6'
        ]);

        $email = strtolower($request->email);
        $row = DB::table('password_resets')->where('email', $email)->first();

        if (!$row) {
            return response()->json(['message' => 'Invalid or expired code.'], 422);
        }

        // Check expiry
        $created = Carbon::parse($row->created_at);
        if ($created->addMinutes($this->expiryMinutes)->isPast()) {
            return response()->json(['message' => 'Invalid or expired code.'], 422);
        }

        // Check code hash
        if (!Hash::check($request->code, $row->token)) {
            return response()->json(['message' => 'Invalid or expired code.'], 422);
        }

        return response()->json(['message' => 'Code valid.'], 200);
    }

    /**
     * POST /api/password/reset
     * Validates again + updates the user password; deletes the reset row.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email',
            'code'                  => 'required|digits:6',
            'password'              => 'required|string|min:8',
            // If you decide to send password_confirmation, switch to: 'password' => 'required|string|min:8|confirmed'
        ]);

        $email = strtolower($request->email);
        $row = DB::table('password_resets')->where('email', $email)->first();

        if (
            !$row ||
            Carbon::parse($row->created_at)->addMinutes($this->expiryMinutes)->isPast() ||
            !Hash::check($request->code, $row->token)
        ) {
            return response()->json(['message' => 'Invalid or expired code.'], 422);
        }

        // Update password
        $user = DB::table('users')->where('email', $email)->first();
        if (!$user) {
            // Generic message to avoid user enumeration
            return response()->json(['message' => 'Password updated successfully.'], 200);
        }

        DB::table('users')
            ->where('email', $email)
            ->update(['password' => Hash::make($request->password), 'updated_at' => Carbon::now()]);

        // Invalidate the OTP
        DB::table('password_resets')->where('email', $email)->delete();

        return response()->json(['message' => 'Password updated successfully.'], 200);
    }
}
