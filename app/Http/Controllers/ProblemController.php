<?php

namespace App\Http\Controllers;

use App\Models\Caller;
use App\Models\Equipment;
use App\Models\Problem;
use App\Models\ProblemType;
use App\Models\Software;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProblemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Require login
    }

    /**
     * Display a listing of problems based on user role.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isSpecialist()) {
            $problems = Problem::where('specialist_id', $user->id)->where('status', '!=', 'resolved')->get();
        } else {
            $problems = Problem::where('status', 'open')->get();
        }
        return view('problems.index', compact('problems'));
    }

    public function create()
    {
        $callers = Caller::all();
        $problemTypes = ProblemType::all();
        $equipment = Equipment::all();
        $software = Software::all();
        return view('problems.create', compact('callers', 'problemTypes', 'equipment', 'software'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'caller_id' => 'required|exists:callers,caller_id',
            'problem_type_id' => 'required|exists:problem_types,problem_type_id',
            'equipment_serial' => 'nullable|exists:equipment,serial_number',
            'software_id' => 'nullable|exists:software,software_id',
            'notes' => 'nullable|string',
        ]);

        Problem::create([
            'caller_id' => $request->caller_id,
            'operator_id' => Auth::id(),
            'problem_type_id' => $request->problem_type_id,
            'equipment_serial' => $request->equipment_serial,
            'software_id' => $request->software_id,
            'status' => 'open',
            'reported_time' => now(),
            'notes' => $request->notes,
        ]);

        return redirect()->route('problems.index')->with('success', 'Problem logged successfully');
    }

    public function assignSpecialist($problemId)
    {
        $problem = Problem::findOrFail($problemId);
        $specialist = $this->assignSpecialistToProblem($problem);

        if ($specialist) {
            return redirect()->back()->with('success', "Specialist assigned: {$specialist->name}");
        }
        return redirect()->back()->with('error', 'No specialist available');
    }

    public function resolve(Request $request, $problemId)
    {
        $problem = Problem::findOrFail($problemId);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->id !== $problem->specialist_id && !$user->isAdmin()) {
            abort(403, 'Unauthorized');
        }
        $request->validate([
            'resolution_notes' => 'required|string',
        ]);
        $problem->status = 'resolved';
        $problem->resolved_time = now();
        $problem->notes .= "\nResolution: " . $request->resolution_notes;
        $problem->save();
        return redirect()->route('problems.index')->with('success', 'Problem resolved successfully');
    }

    private function assignSpecialistToProblem(Problem $problem)
    {
        $ancestors = $this->getAncestorProblemTypes($problem->problem_type_id);
        $allTypes = array_merge([$problem->problem_type_id], $ancestors);

        $specialists = User::where('role', 'specialist')
            ->whereHas('expertise', function ($query) use ($allTypes) {
                $query->whereIn('specialist_expertise.problem_type_id', $allTypes);
            })->get();

        if ($specialists->isEmpty()) {
            return null;
        }

        $leastLoaded = $specialists->sortBy('workload')->first();
        if ($leastLoaded->workload >= 10) { // Example max workload
            return null;
        }

        $problem->specialist_id = $leastLoaded->id;
        $problem->status = 'assigned';
        $problem->save();

        return $leastLoaded;
    }

    private function getAncestorProblemTypes($problemTypeId)
    {
        $ancestors = [];
        $current = ProblemType::find($problemTypeId);

        while ($current && $current->parent_type_id) {
            $ancestors[] = $current->parent_type_id;
            $current = ProblemType::find($current->parent_type_id);
        }

        return $ancestors;
    }
}