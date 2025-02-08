<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(Admin::all());
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

        $admin = Admin::updateOrCreate(
            ['user_id' => Auth::id()],
            $request->all()
        );

        return response()->json([
            'message' => 'Admin profile updated successfully',
            'admin' => $admin
        ], 201);
    }

    public function show($id)
    {
        $admin = Admin::findOrFail($id);

        if (Auth::id() !== $admin->user_id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($admin);
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        if (Auth::id() !== $admin->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $admin->update($request->all());
        return response()->json($admin);
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        Admin::destroy($id);
        return response()->json(['message' => 'Admin deleted successfully']);
    }
}
