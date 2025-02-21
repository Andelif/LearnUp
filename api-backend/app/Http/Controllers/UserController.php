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

        $user = DB::select("SELECT * FROM users WHERE email = ?", [$request->email]);

        if (empty($user) || !Hash::check($request->password, $user[0]->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }

        $user = $user[0]; // Get the first result from the array of stdClass objects

        // Use Eloquent to create the token
        $userModel = User::find($user->id); // Load the user with Eloquent
        $token = $userModel->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'Login successful'
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

    // Logout user
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
