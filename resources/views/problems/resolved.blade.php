@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0">Resolved Problems</h1>
                <a href="{{ route('problems.index') }}" class="btn btn-primary btn-sm">Back to Current Problems</a>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search by problem #, caller, notes, or problem type" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="unsolvable" {{ request('status') == 'unsolvable' ? 'selected' : '' }}>Unsolvable</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>

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
                                <th>Resolved Time</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($problems as $problem)
                                <tr>
                                    <td data-label="Problem #">{{ $problem->problem_number }}</td>
                                    <td data-label="Caller">{{ $problem->caller->name }}</td>
                                    <td data-label="Type">{{ $problem->problemType->name }}</td>
                                    <td data-label="Status">
                                        @switch($problem->status)
                                            @case('resolved')
                                                <span class="badge bg-success">{{ ucfirst($problem->status) }}</span>
                                                @break
                                            @case('unsolvable')
                                                <span class="badge bg-dark">{{ ucfirst($problem->status) }}</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($problem->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td data-label="Specialist">{{ $problem->specialist ? $problem->specialist->name : 'N/A' }}</td>
                                    <td data-label="Resolved Time">
                                        {{ $problem->resolved_time ? $problem->resolved_time->format('Y-m-d H:i') : 'N/A' }}
                                    </td>
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No resolved problems found.</td> <!-- Updated colspan -->
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
@endsection