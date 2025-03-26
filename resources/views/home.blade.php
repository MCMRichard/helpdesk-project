@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Dashboard</h1>
    <div class="row">
        <!-- Open Problems -->
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Open Problems</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $openProblems }}</h5>
                </div>
            </div>
        </div>

        <!-- Assigned Problems -->
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Assigned Problems</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $assignedProblems }}</h5>
                </div>
            </div>
        </div>

        <!-- Resolved Problems -->
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Resolved Problems</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $resolvedProblems }}</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Button to View All Problems -->
    <a href="{{ route('problems.index') }}" class="btn btn-primary">View Problems</a>
</div>
@endsection
