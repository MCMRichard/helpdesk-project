@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <h1>{{ Auth::user()->role === 'specialist' ? 'My Assigned Problems' : 'Open Problems' }}</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Problem #</th>
                <th>Caller</th>
                <th>Type</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($problems as $problem)
                <tr>
                    <td>{{ $problem->problem_number }}</td>
                    <td>{{ $problem->caller->name }}</td>
                    <td>{{ $problem->problemType->name }}</td>
                    <td>{{ $problem->status }}</td>
                    <td>
                        @if (Auth::user()->role !== 'specialist')
                            <form action="{{ route('problems.assign', $problem->problem_number) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">Assign Specialist</button>
                            </form>
                        @endif
                        @if ($problem->specialist_id == Auth::id() || Auth::user()->isAdmin())
                            <form action="{{ route('problems.resolve', $problem->problem_number) }}" method="POST">
                                @csrf
                                <textarea name="resolution_notes" placeholder="Resolution notes" required></textarea>
                                <button type="submit" class="btn btn-success">Resolve</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('problems.create') }}" class="btn btn-success">Log New Problem</a>
@endsection
