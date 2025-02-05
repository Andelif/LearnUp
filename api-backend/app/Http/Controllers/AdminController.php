<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;

class AdminController extends Controller
{
    public function index()
    {
        return response()->json(Admin::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'full_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:15',
            'permission_req' => 'nullable|boolean',
            'match_made' => 'nullable|integer|min:0',
            'task_assigned' => 'nullable|integer|min:0'
        ]);

        $admin = Admin::create($request->all());
        return response()->json([
            'message' => 'Admin profile created successfully',
            'admin' => $admin
        ], 201);
    }

    public function show($id)
    {
        return response()->json(Admin::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);
        $admin->update($request->all());
        return response()->json($admin);
    }

    public function destroy($id)
    {
        Admin::destroy($id);
        return response()->json(['message' => 'Admin deleted successfully']);
    }
}
