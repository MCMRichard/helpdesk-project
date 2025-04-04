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

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isSpecialist()) {
            $problems = Problem::with('specialist')
                ->where('specialist_id', $user->id)
                ->where('status', '!=', 'resolved')
                ->get();
        } else {
            $problems = Problem::with('specialist')
                ->where('status', 'open')
                ->get();
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
        $specialists = User::where('role', 'specialist')
            ->whereHas('expertise', function ($query) use ($problem) {
                $allTypes = array_merge([$problem->problem_type_id], $this->getAncestorProblemTypes($problem->problem_type_id));
                $query->whereIn('specialist_expertise.problem_type_id', $allTypes);
            })
            ->withCount('activeAssignments')
            ->get();

        if ($specialists->isEmpty()) {
            return redirect()->back()->with('error', 'No specialist with matching expertise available');
        }
        return redirect()->back()->with('error', 'All specialists are at maximum workload');
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

    public function activeAssignments()
    {
        return $this->hasMany(Problem::class, 'specialist_id')->where('status', '!=', 'resolved');
    }

    public function adminIndex(Request $request)
    {
        $query = Problem::with(['caller', 'problemType', 'specialist', 'operator']);
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('notes', 'like', '%' . $request->search . '%')
                ->orWhereHas('caller', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
            });
        }
        $problems = $query->orderBy('reported_time', 'desc')->get();
        return view('admin.problems', compact('problems'));
    }

    public function resolved()
    {
        $problems = Problem::with(['caller', 'problemType', 'specialist'])
            ->where('status', 'resolved')
            ->orderBy('resolved_time', 'desc')
            ->get();
        return view('problems.resolved', compact('problems'));
    }

    private function assignSpecialistToProblem(Problem $problem)
    {
        $ancestors = $this->getAncestorProblemTypes($problem->problem_type_id);
        $allTypes = array_merge([$problem->problem_type_id], $ancestors);

        $specialists = User::where('role', 'specialist')
            ->whereHas('expertise', function ($query) use ($allTypes) {
                $query->whereIn('specialist_expertise.problem_type_id', $allTypes);
            })
            ->withCount('activeAssignments')
            ->get();

        if ($specialists->isEmpty()) {
            return null;
        }

        $leastLoaded = $specialists->sortBy('active_assignments_count')->first();
        if ($leastLoaded->active_assignments_count >= 10) { // Max 10 active problems
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
