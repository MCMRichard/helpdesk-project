<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Problem; // Import Problem model

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $openProblems = Problem::where('status', 'open')->count();
        $assignedProblems = Problem::where('status', 'assigned')->count();
        $resolvedProblems = Problem::where('status', 'resolved')->count();

        return view('home', compact('openProblems', 'assignedProblems', 'resolvedProblems'));
    }
}
