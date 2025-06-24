<?php

namespace App\Http\Controllers;

use App\Models\Caller;
use App\Models\Equipment;
use App\Models\Problem;
use App\Models\ProblemAssignmentHistory;
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
    
        // Process notes for each problem
        $problems = $problems->map(function ($problem) {
            $notes = $problem->notes ?? '';
            $parsedNotes = [
                'initial' => '',
                'resolution' => [],
                'unassignments' => [],
                'unsolvable' => [],
                'edits' => [],
            ];
    
            // Split notes by newlines
            $lines = explode("\n", $notes);
            $initialNotes = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }
                if (str_starts_with($line, 'Resolution: ')) {
                    $parsedNotes['resolution'][] = substr($line, strlen('Resolution: '));
                } elseif (preg_match('/^Unassigned by .+: (.+)$/', $line, $matches)) {
                    $parsedNotes['unassignments'][] = $matches[1];
                } elseif (preg_match('/^Marked unsolvable by .+: (.+)$/', $line, $matches)) {
                    $parsedNotes['unsolvable'][] = $matches[1];
                } elseif (preg_match('/^Edited by .+ on .+$/', $line)) {
                    $parsedNotes['edits'][] = $line;
                } else {
                    $initialNotes[] = $line;
                }
            }
            $parsedNotes['initial'] = implode(' ', $initialNotes);
            $problem->parsedNotes = $parsedNotes;
            return $problem;
        });
    
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
        /** @var \App\Models\User $user */
        $user = Auth::user();
    
        // Restrict specialists from assigning problems
        if ($user->isSpecialist()) {
            abort(403, 'Unauthorized');
        }
    
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

        // Allow admins, operators, or assigned specialists to resolve
        if (!$user->isAdmin() && !$user->isOperator() && $user->id !== $problem->specialist_id) {
            return redirect()->route('problems.index')->with('error', 'You are not authorized to resolve this problem.');
        }

        $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        // Update assignment history
        $assignment = ProblemAssignmentHistory::where('problem_id', $problem->problem_number)
            ->where('specialist_id', $problem->specialist_id)
            ->whereNull('unassigned_at')
            ->latest('assigned_at')
            ->first();

        if ($assignment) {
            $assignment->unassigned_at = now();
            $assignment->reason = "Problem resolved: {$request->resolution_notes}";
            $assignment->save();
        }

        $problem->status = 'resolved';
        $problem->resolved_time = now();
        $problem->notes .= "\nResolution: " . $request->resolution_notes;
        $problem->specialist_id = null; // Clear specialist_id
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
    
        // Apply status filter only if a valid status is selected
        if ($request->has('status') && $request->status && in_array($request->status, ['open', 'assigned', 'resolved', 'unsolvable'])) {
            $query->where('status', $request->status);
        }
    
        // Apply search filter
        if ($request->has('search') && $request->search !== '') {
            $searchTerm = trim($request->search);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('notes', 'like', '%' . $searchTerm . '%')
                  ->orWhere('problem_number', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('caller', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%');
                  })
                  ->orWhereHas('problemType', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }
    
        $problems = $query->orderBy('reported_time', 'desc')->get();
    
        // Process notes for each problem
        $problems = $problems->map(function ($problem) {
            $notes = $problem->notes ?? '';
            $parsedNotes = [
                'initial' => '',
                'resolution' => [],
                'unassignments' => [],
                'unsolvable' => [],
                'edits' => [],
            ];
    
            // Split notes by newlines
            $lines = explode("\n", $notes);
            $initialNotes = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }
                if (str_starts_with($line, 'Resolution: ')) {
                    $parsedNotes['resolution'][] = substr($line, strlen('Resolution: '));
                } elseif (preg_match('/^Unassigned by .+: (.+)$/', $line, $matches)) {
                    $parsedNotes['unassignments'][] = $matches[1];
                } elseif (preg_match('/^Marked unsolvable by .+: (.+)$/', $line, $matches)) {
                    $parsedNotes['unsolvable'][] = $matches[1];
                } elseif (preg_match('/^Edited by .+ on .+$/', $line)) {
                    $parsedNotes['edits'][] = $line;
                } else {
                    $initialNotes[] = $line;
                }
            }
            $parsedNotes['initial'] = implode(' ', $initialNotes);
            $problem->parsedNotes = $parsedNotes;
            return $problem;
        });
    
        return view('admin.problems', compact('problems'));
    }

    public function resolved(Request $request)
    {
        $query = Problem::with(['caller', 'problemType', 'specialist'])
            ->whereIn('status', ['resolved', 'unsolvable']);
        
        // Apply search filter
    if ($request->has('search') && $request->search !== '') {
        $searchTerm = trim($request->search);
        $query->where(function ($q) use ($searchTerm) {
            $q->where('notes', 'like', '%' . $searchTerm . '%')
            ->orWhere('problem_number', 'like', '%' . $searchTerm . '%')
            ->orWhereHas('caller', function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%');
            })
            ->orWhereHas('problemType', function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%');
            });
        });
    }
    
        // Apply status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
    
        $problems = $query->orderBy('resolved_time', 'desc')->get();
    
        // Process notes for each problem
        $problems = $problems->map(function ($problem) {
            $notes = $problem->notes ?? '';
            $parsedNotes = [
                'initial' => '',
                'resolution' => [],
                'unassignments' => [],
                'unsolvable' => [],
                'edits' => [],
            ];
    
            // Split notes by newlines
            $lines = explode("\n", $notes);
            $initialNotes = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }
                if (str_starts_with($line, 'Resolution: ')) {
                    $parsedNotes['resolution'][] = substr($line, strlen('Resolution: '));
                } elseif (preg_match('/^Unassigned by .+: (.+)$/', $line, $matches)) {
                    $parsedNotes['unassignments'][] = $matches[1];
                } elseif (preg_match('/^Marked unsolvable by .+: (.+)$/', $line, $matches)) {
                    $parsedNotes['unsolvable'][] = $matches[1];
                } elseif (preg_match('/^Edited by .+ on .+$/', $line)) {
                    $parsedNotes['edits'][] = $line;
                } else {
                    $initialNotes[] = $line;
                }
            }
            $parsedNotes['initial'] = implode(' ', $initialNotes);
            $problem->parsedNotes = $parsedNotes;
            return $problem;
        });
    
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
            ->whereDoesntHave('assignmentHistory', function ($query) use ($problem) {
                $query->where('problem_id', $problem->problem_number)
                      ->whereNotNull('unassigned_at');
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
    
        // Record the assignment in history
        ProblemAssignmentHistory::create([
            'problem_id' => $problem->problem_number,
            'specialist_id' => $leastLoaded->id,
            'assigned_at' => now(),
        ]);
    
        return $leastLoaded;
    }

    public function unassignSpecialist(Request $request, $problemId)
    {
        $problem = Problem::findOrFail($problemId);
        $user = Auth::user();
        
         /** @var \App\Models\User $user */
         $user = Auth::user();
        if ($user->id !== $problem->specialist_id && !$user->isAdmin()) {
            abort(403, 'Unauthorized');
        }
    
        $request->validate([
            'unassign_reason' => 'required|string',
        ]);
    
        // Find the current assignment record
        $assignment = ProblemAssignmentHistory::where('problem_id', $problem->problem_number)
            ->where('specialist_id', $problem->specialist_id)
            ->whereNull('unassigned_at')
            ->latest('assigned_at')
            ->first();
    
        if ($assignment) {
            $assignment->unassigned_at = now();
            $assignment->reason = $request->unassign_reason;
            $assignment->save();
        }
    
        $problem->specialist_id = null;
        $problem->status = 'open';
        $problem->notes .= "\nUnassigned by {$user->name}: " . $request->unassign_reason;
        $problem->save();
    
        return redirect()->route('problems.index')->with('success', 'Problem returned to operator');
    }

    public function markUnsolvable(Request $request, $problemId)
    {
        $problem = Problem::findOrFail($problemId);
        /** @var \App\Models\User $user */
        $user = Auth::user();
    
        // Restrict to admins or assigned specialists only
        if (!$user->isAdmin() && $user->id !== $problem->specialist_id) {
            return redirect()->route('problems.index')->with('error', 'You are not authorized to mark this problem as unsolvable.');
        }
    
        $request->validate([
            'unsolvable_reason' => 'required|string',
        ]);
    
        // Update assignment history
        $assignment = ProblemAssignmentHistory::where('problem_id', $problem->problem_number)
            ->where('specialist_id', $problem->specialist_id)
            ->whereNull('unassigned_at')
            ->latest('assigned_at')
            ->first();
    
        if ($assignment) {
            $assignment->unassigned_at = now();
            $assignment->reason = "Marked unsolvable: {$request->unsolvable_reason}";
            $assignment->save();
        }
    
        $problem->status = 'unsolvable';
        $problem->unsolvable_reason = $request->unsolvable_reason;
        $problem->notes .= "\nMarked unsolvable by {$user->name}: " . $request->unsolvable_reason;
        $problem->specialist_id = null;
        $problem->resolved_time = now();
        $problem->save();
    
        return redirect()->route('problems.index')->with('success', 'Problem marked as unsolvable');
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

    public function edit($problemId)
    {
        $problem = Problem::findOrFail($problemId);
        /** @var \App\Models\User $user */
        $user = Auth::user();
    
        // Restrict to admins or operators
        if (!$user->isAdmin() && !$user->isOperator()) {
            abort(403, 'Unauthorized');
        }
    
        $callers = Caller::all();
        $problemTypes = ProblemType::all();
        $equipment = Equipment::all();
        $software = Software::all();
    
        return view('problems.edit', compact('problem', 'callers', 'problemTypes', 'equipment', 'software'));
    }
    
    public function update(Request $request, $problemId)
    {
        $problem = Problem::findOrFail($problemId);
        /** @var \App\Models\User $user */
        $user = Auth::user();
    
        // Restrict to admins or operators
        if (!$user->isAdmin() && !$user->isOperator()) {
            abort(403, 'Unauthorized');
        }
    
        $request->validate([
            'caller_id' => 'required|exists:callers,caller_id',
            'problem_type_id' => 'required|exists:problem_types,problem_type_id',
            'equipment_serial' => 'nullable|exists:equipment,serial_number',
            'software_id' => 'nullable|exists:software,software_id',
            'notes' => 'nullable|string',
        ]);
    
        $problem->update([
            'caller_id' => $request->caller_id,
            'problem_type_id' => $request->problem_type_id,
            'equipment_serial' => $request->equipment_serial,
            'software_id' => $request->software_id,
            'notes' => $request->notes,
        ]);
    
        $problem->notes .= "\nEdited by {$user->name} on " . now()->format('Y-m-d H:i');
        $problem->save();
    
        return redirect()->route('problems.index')->with('success', 'Problem updated successfully');
    }
}
