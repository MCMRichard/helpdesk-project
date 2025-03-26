<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    // Redirect path after registration
    protected $redirectTo = '/dashboard';

    // Constructor for guest middleware
    public function __construct()
    {
        $this->middleware('guest');
    }

    // Validation rules for registration
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:specialist,operator,admin'],
            'problem_type_ids' => ['nullable', 'array', 'required_if:role,specialist'],
            'problem_type_ids.*' => ['exists:problem_types,problem_type_id'],
        ]);
    }
    
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);
    
        if ($data['role'] === 'specialist' && !empty($data['problem_type_ids'])) {
            $user->expertise()->attach($data['problem_type_ids']);
        }
    
        return $user;
    }
}