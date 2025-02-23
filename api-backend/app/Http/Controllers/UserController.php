<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\HasApiTokens;
use App\Models\User;

class UserController extends Controller
{
    use HasApiTokens;

    public function register(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|string|in:learner,tutor,admin',
            'contact_number' => 'nullable|string|max:20',
            'gender' => 'nullable|string|in:male,female,other'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            // Insert user into users table
            DB::insert("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)", [
                $request->name,
                $request->email,
                Hash::make($request->password),
                $request->role
            ]);

            // Get the newly created user ID
            $userId = DB::getPdo()->lastInsertId();

            // Insert into role-specific tables
            if ($request->role === 'learner') {
                DB::insert("INSERT INTO learners (user_id, full_name, contact_number, gender, address) VALUES (?, ?, ?, ?, ?)", [
                    $userId,
                    $request->name,
                    $request->contact_number ?? null, 
                    $request->gender ?? null,  
                    null   // Placeholder for address
                ]);
            } elseif ($request->role === 'tutor') {
                DB::insert("INSERT INTO tutors (user_id, full_name, contact_number, gender, qualification, experience, preferred_salary, availability, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                    $userId,
                    $request->name,
                    $request->contact_number ?? null, 
                    $request->gender ?? null,
                    null,  // Placeholder for qualifications
                    null,  // Placeholder for experience
                    null,  // Placeholder for preferred salary
                    null,  // Placeholder for availability
                    null   // Placeholder for address
                ]);
            } elseif ($request->role === 'admin') {
                DB::insert("INSERT INTO admins (user_id, full_name) VALUES (?, ?)", [$userId, $request->name]);
            }

            // Now use Eloquent for token creation
            $userModel = User::find($userId); // Load the user with Eloquent
            $token = $userModel->createToken('auth_token')->plainTextToken;
            $token = $userModel->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully',
                'user' => [
                    'id' => $userId,
                    'name' => $request->name,
                    'email' => $request->email,
                    'role' => $request->role,
                ],
                'token' => $token
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Registration failed', 'details' => $e->getMessage()], 500);
        }
    }

    // Login user
    public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    $email = $request->email;
    $password = $request->password;

    // ✅ Special condition: Auto-assign admin role for fixed credentials
    if ($email === 'admin1@gmail.com' && $password === '12121212') {
        $adminUser = DB::select("SELECT * FROM users WHERE email = ?", [$email]);

        // If admin user doesn't exist, create one
        if (empty($adminUser)) {
            DB::insert("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)", [
                'Admin',
                $email,
                Hash::make($password),
                'admin'
            ]);

            // Fetch the newly created admin user
            $adminUser = DB::select("SELECT * FROM users WHERE email = ?", [$email]);
        }

        $adminUser = $adminUser[0]; // Convert array result to object
        $userModel = User::find($adminUser->id);
        $token = $userModel->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Admin login successful',
            'user' => [
                'id' => $adminUser->id,
                'name' => $adminUser->name,
                'email' => $adminUser->email,
                'role' => 'admin',
            ],
            'token' => $token,
            'redirect' => '/admin/dashboard'
        ], 200);
    }

    // ✅ Normal user login process
    $user = DB::select("SELECT * FROM users WHERE email = ?", [$email]);

    if (empty($user) || !Hash::check($password, $user[0]->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.']
        ]);
    }

    $user = $user[0]; // Convert array result to object
    $userModel = User::find($user->id);
    $token = $userModel->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ],
        'token' => $token,
        'redirect' => ($user->role === 'admin') ? '/admin/dashboard' : '/dashboard'
    ]);
}

    // Get authenticated user
    public function getUser(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);
    }
    
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Update the user's name
        DB::update("UPDATE users SET name = ? WHERE id = ?", [$request->name, $user->id]);

        return response()->json(['message' => 'User profile updated successfully']);
    }


    // Logout user
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
