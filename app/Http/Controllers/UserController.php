<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Method to list all users
    public function index()
    {
        $users = User::with('expertise')->get();
        return view('admin.users', compact('users'));
    }

    // Method to show the create user form
    public function create()
    {
        return view('admin.users.create');
    }

    public function edit(User $user)
    {
        return view('admin.edit', compact('user'));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }

    // Update the user
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:specialist,operator,admin'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return redirect()->route('admin.users')->with('success', 'User updated successfully');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:specialist,operator,admin'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully');
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
        $request->validate([
            'problem_type_ids' => 'nullable|array',
            'problem_type_ids.*' => 'exists:problem_types,problem_type_id',
        ]);

        $user = User::findOrFail($userId);

        if ($user->role === 'specialist') {
            $user->expertise()->sync($request->input('problem_type_ids', []));
            return redirect()->back()->with('success', 'Specialties updated successfully.');
        }

        return redirect()->back()->with('error', 'User must be a specialist to update specialties.');
    }
}