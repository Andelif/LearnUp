<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * POST /api/register
     * Validates, creates the user (Eloquent), and returns a token.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|string|min:6|confirmed',
            'role'              => 'required|string|in:learner,tutor,admin',
            'gender'            => 'nullable|string|in:male,female,other,Male,Female,Other',
            'contact_number'    => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            // 400 with field errors (your frontend already handles this shape)
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            $user  = $this->userService->register($validator->validated());
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully',
                'user'    => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role,
                ],
                'token'  => $token,
            ], 201);
        } catch (\Throwable $e) {
            // 500 with detail for debugging
            return response()->json([
                'error'   => 'Registration failed',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/login
     * (kept close to your original, but minor tidy)
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $email    = $request->email;
        $password = $request->password;

        // Special case admin bootstrap
        if ($email === 'admin1@gmail.com' && $password === '12121212') {
            $adminRow = DB::select("SELECT * FROM users WHERE email = ?", [$email]);

            if (empty($adminRow)) {
                DB::insert("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)", [
                    'Admin',
                    $email,
                    Hash::make($password),
                    'admin',
                ]);

                $adminRow = DB::select("SELECT * FROM users WHERE email = ?", [$email]);
            }

            $admin = $adminRow[0];
            $user  = User::findOrFail($admin->id);
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message'  => 'Admin login successful',
                'user'     => [
                    'id'    => $admin->id,
                    'name'  => $admin->name,
                    'email' => $admin->email,
                    'role'  => 'admin',
                ],
                'token'    => $token,
                'redirect' => '/admin/dashboard',
            ], 200);
        }

        // Normal users
        $row = DB::select("SELECT * FROM users WHERE email = ?", [$email]);
        if (empty($row) || !Hash::check($password, $row[0]->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }

        $dbUser = $row[0];
        $user   = User::findOrFail($dbUser->id);
        $token  = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'  => 'Login successful',
            'user'     => [
                'id'    => $dbUser->id,
                'name'  => $dbUser->name,
                'email' => $dbUser->email,
                'role'  => $dbUser->role,
            ],
            'token'    => $token,
            'redirect' => ($dbUser->role === 'admin') ? '/admin/dashboard' : '/dashboard',
        ], 200);
    }

    /**
     * GET /api/user  (auth:sanctum)
     */
    public function getUser(Request $request)
    {
        $u = $request->user();

        return response()->json([
            'id'    => $u->id,
            'name'  => $u->name,
            'email' => $u->email,
            'role'  => $u->role,
        ], 200);
    }

    /**
     * PUT /api/user/update-profile  (auth:sanctum)
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            $this->userService->updateProfile($user->id, $request->name);
            return response()->json(['message' => 'User profile updated successfully'], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'error'   => 'Profile update failed',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/logout  (auth:sanctum)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
