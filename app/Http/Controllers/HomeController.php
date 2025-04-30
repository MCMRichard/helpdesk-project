<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Basic counts
        $openProblems = Problem::where('status', 'open')->count();
        $assignedProblems = Problem::where('status', 'assigned')->count();
        $resolvedProblems = Problem::where('status', 'resolved')->count();
        $unsolvableProblems = Problem::where('status', 'unsolvable')->count();

        // Average resolution time (in minutes) for resolved problems
        $avgResolutionTime = Problem::where('status', 'resolved')
            ->avg(DB::raw('TIMESTAMPDIFF(MINUTE, reported_time, resolved_time)'));

        // Specialist workloads (active assignments per specialist)
        $specialistWorkloads = User::where('role', 'specialist')
            ->withCount(['activeAssignments' => function ($query) {
                $query->whereIn('status', ['open', 'assigned']);
            }])
            ->get()
            ->map(function ($specialist) {
                return [
                    'name' => $specialist->name,
                    'workload' => $specialist->active_assignments_count,
                ];
            });

        // Problem status distribution for chart
        $statusDistribution = [
            'open' => $openProblems,
            'assigned' => $assignedProblems,
            'resolved' => $resolvedProblems,
            'unsolvable' => $unsolvableProblems,
        ];

        return view('dashboard', compact(
            'openProblems',
            'assignedProblems',
            'resolvedProblems',
            'unsolvableProblems',
            'avgResolutionTime',
            'specialistWorkloads',
            'statusDistribution'
        ));
    }
}