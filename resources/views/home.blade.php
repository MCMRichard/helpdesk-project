@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h1 class="h4 mb-0">Dashboard</h1>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <!-- Open Problems -->
                    <div class="col-md-4">
                        <div class="card text-white bg-primary shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Open Problems</h5>
                                <p class="display-6">{{ $openProblems }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Assigned Problems -->
                    <div class="col-md-4">
                        <div class="card text-white bg-warning shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Assigned Problems</h5>
                                <p class="display-6">{{ $assignedProblems }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Resolved Problems -->
                    <div class="col-md-4">
                        <div class="card text-white bg-success shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Resolved Problems</h5>
                                <p class="display-6">{{ $resolvedProblems }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('problems.index') }}" class="btn btn-primary">View Problems</a>
                </div>
            </div>
        </div>
    </div>
@endsection