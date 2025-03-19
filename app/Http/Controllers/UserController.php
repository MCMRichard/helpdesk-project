<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Method to list all users
    public function index()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    // Method to update user roles
    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:operator,specialist,admin']);
        $user->update(['role' => $request->role]);
        return redirect()->route('admin.users')->with('success', 'Role updated successfully');
    }
}
