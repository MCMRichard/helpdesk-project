@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>All Current Problems</h1>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Filter Form -->
        <form method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by caller or notes" value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <table class="table table-striped">
            <thead>
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
                @foreach ($problems as $problem)
                    <tr>
                        <td>{{ $problem->problem_number }}</td>
                        <td>{{ $problem->caller->name }}</td>
                        <td>{{ $problem->problemType->name }}</td>
                        <td>{{ ucfirst($problem->status) }}</td>
                        <td>{{ $problem->specialist ? $problem->specialist->name : 'Unassigned' }}</td>
                        <td>{{ $problem->reported_time->format('Y-m-d H:i') }}</td>
                        <td>{{ $problem->notes }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection