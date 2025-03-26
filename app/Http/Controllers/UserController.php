<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Method to list all users
    public function index()
    {
        $users = User::with('expertise')->get();
        return view('admin.users', compact('users'));
    }

    // Method to update user roles
    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:operator,specialist,admin']);
        $user->update(['role' => $request->role]);
        return redirect()->route('admin.users')->with('success', 'Role updated successfully');
    }

    public function updateExpertise(Request $request, $userId)
    {
        // Validate the request
        $request->validate([
            'problem_type_ids' => 'nullable|array',
            'problem_type_ids.*' => 'exists:problem_types,problem_type_id',
        ]);

        $user = User::findOrFail($userId);

        if ($user->role === 'specialist') {
            $user->expertise()->sync($request->input('problem_type_ids', []));
            return redirect()->back()->with('success', 'Specialties updated successfully.');
        }

        // Handle invalid role
        return redirect()->back()->with('error', 'User must be a specialist to update specialties.');
    }
}
