@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Resolved Problems</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Problem #</th>
                    <th>Caller</th>
                    <th>Type</th>
                    <th>Specialist</th>
                    <th>Resolved Time</th>
                    <th>Resolution Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($problems as $problem)
                    <tr>
                        <td>{{ $problem->problem_number }}</td>
                        <td>{{ $problem->caller->name }}</td>
                        <td>{{ $problem->problemType->name }}</td>
                        <td>{{ $problem->specialist ? $problem->specialist->name : 'N/A' }}</td>
                        <td>{{ $problem->resolved_time->format('Y-m-d H:i') }}</td>
                        <td>{{ $problem->notes }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ route('problems.index') }}" class="btn btn-primary">Back to Current Problems</a>
    </div>
@endsection