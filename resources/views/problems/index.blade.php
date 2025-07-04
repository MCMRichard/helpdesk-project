@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0">
                    {{ Auth::user()->role === 'specialist' ? 'My Assigned Problems' : 'Open Problems' }}
                </h1>
                <div>
                    <a href="{{ route('problems.create') }}" class="btn btn-success btn-sm me-2">Log New Problem</a>
                    <a href="{{ route('problems.resolved') }}" class="btn btn-info btn-sm">View Resolved Problems</a>
                </div>
            </div>
            <div class="card-body">
                <!-- Success Alert -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Error Alert -->
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Problems Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Problem #</th>
                                <th>Caller</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Specialist</th>
                                <th>Equipment Status</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($problems as $problem)
                                <tr>
                                    <td data-label="Problem #">{{ $problem->problem_number }}</td>
                                    <td data-label="Caller">{{ $problem->caller->name }}</td>
                                    <td data-label="Type">{{ $problem->problemType->name }}</td>
                                    <td data-label="Status">{{ $problem->status }}</td>
                                    <td data-label="Specialist">
                                        {{ $problem->specialist ? $problem->specialist->name : 'No specialist assigned' }}
                                        @if ($problem->specialist)
                                            (Workload: {{ $problem->specialist->workload }})
                                        @endif
                                    </td>
                                    <td data-label="Equipment Status">{{ $problem->equipment ? $problem->equipment->status : 'N/A' }}</td>
                                    <td data-label="Notes">
                                        @if ($problem->parsedNotes['initial'] || $problem->parsedNotes['resolution'] || $problem->parsedNotes['unassignments'] || $problem->parsedNotes['unsolvable'] || $problem->parsedNotes['edits'])
                                            <div class="accordion" id="notesAccordion{{ $problem->problem_number }}">
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="notesHeading{{ $problem->problem_number }}">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#notesCollapse{{ $problem->problem_number }}" aria-expanded="false" aria-controls="notesCollapse{{ $problem->problem_number }}">
                                                            View Notes
                                                        </button>
                                                    </h2>
                                                    <div id="notesCollapse{{ $problem->problem_number }}" class="accordion-collapse collapse" aria-labelledby="notesHeading{{ $problem->problem_number }}" data-bs-parent="#notesAccordion{{ $problem->problem_number }}">
                                                        <div class="accordion-body">
                                                            @if ($problem->parsedNotes['initial'])
                                                                <h6>Initial Notes</h6>
                                                                <p>{{ $problem->parsedNotes['initial'] }}</p>
                                                            @endif
                                                            @if ($problem->parsedNotes['resolution'])
                                                                <h6>Resolution</h6>
                                                                <ul>
                                                                    @foreach ($problem->parsedNotes['resolution'] as $resolution)
                                                                        <li>{{ $resolution }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                            @if ($problem->parsedNotes['unassignments'])
                                                                <h6>Unassignment Reasons</h6>
                                                                <ul>
                                                                    @foreach ($problem->parsedNotes['unassignments'] as $reason)
                                                                        <li>{{ $reason }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                            @if ($problem->parsedNotes['unsolvable'])
                                                                <h6>Unsolvable Reasons</h6>
                                                                <ul>
                                                                    @foreach ($problem->parsedNotes['unsolvable'] as $reason)
                                                                        <li>{{ $reason }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                            @if ($problem->parsedNotes['edits'])
                                                                <h6>Edit History</h6>
                                                                <ul>
                                                                    @foreach ($problem->parsedNotes['edits'] as $edit)
                                                                        <li>{{ $edit }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">No notes</span>
                                        @endif
                                    </td>
                                    <td data-label="Actions">
                                        <div class="d-flex flex-column gap-2">
                                            @if (Auth::user()->role !== 'specialist')
                                                <form action="{{ route('problems.assign', $problem->problem_number) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-sm w-100">Assign Specialist</button>
                                                </form>
                                            @endif
                                            @if (Auth::user()->isAdmin() || Auth::user()->isOperator() || Auth::id() === $problem->specialist_id)
                                                <!-- Resolve Form -->
                                                <form action="{{ route('problems.resolve', $problem->problem_number) }}" method="POST">
                                                    @csrf
                                                    <textarea name="resolution_notes" class="form-control mb-2" placeholder="Resolution notes" rows="2" required></textarea>
                                                    <button type="submit" class="btn btn-success btn-sm w-100">Resolve</button>
                                                </form>
                                            @endif
                                            @if ($problem->specialist_id && (Auth::user()->isAdmin() || Auth::id() === $problem->specialist_id))
                                                <!-- Unassign Form -->
                                                <form action="{{ route('problems.unassign', $problem->problem_number) }}" method="POST">
                                                    @csrf
                                                    <textarea name="unassign_reason" class="form-control mb-2" placeholder="Reason for unassigning" rows="2" required></textarea>
                                                    <button type="submit" class="btn btn-warning btn-sm w-100">Unassign</button>
                                                </form>
                                            @endif
                                            @if (Auth::user()->isAdmin() || Auth::id() === $problem->specialist_id)
                                                <!-- Mark Unsolvable Form -->
                                                <form action="{{ route('problems.unsolvable', $problem->problem_number) }}" method="POST">
                                                    @csrf
                                                    <textarea name="unsolvable_reason" class="form-control mb-2" placeholder="Reason for marking unsolvable (e.g., requires replacement)" rows="2" required></textarea>
                                                    <button type="submit" class="btn btn-danger btn-sm w-100">Mark Unsolvable</button>
                                                </form>
                                            @endif
                                            @if (Auth::user()->isAdmin() || Auth::user()->isOperator())
                                                <!-- Edit Button -->
                                                <a href="{{ route('problems.edit', $problem->problem_number) }}" class="btn btn-info btn-sm w-100">Edit</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No problems found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection