@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h1 class="h4 mb-0">All Current Problems</h1>
                <a href="{{ route('problems.create') }}" class="btn btn-primary btn-sm">Log New Problem</a>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Filter Form -->
                <form method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search by caller or notes" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
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
                                <th>Reported Time</th>
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
                                            @case('open')
                                                <span class="badge bg-danger">{{ ucfirst($problem->status) }}</span>
                                                @break
                                            @case('assigned')
                                                <span class="badge bg-warning">{{ ucfirst($problem->status) }}</span>
                                                @break
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
                                    <td data-label="Specialist">{{ $problem->specialist ? $problem->specialist->name : 'Unassigned' }}</td>
                                    <td data-label="Reported Time">{{ $problem->reported_time->format('Y-m-d H:i') }}</td>
                                    <td data-label="Notes">
                                        {{ $problem->notes }}
                                        @if($problem->unsolvable_reason)
                                            <br><strong>Unsolvable Reason:</strong> {{ $problem->unsolvable_reason }}
                                        @endif
                                    </td>                                    
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No problems found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
