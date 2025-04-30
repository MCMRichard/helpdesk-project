@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h1 class="h4 mb-0">Dashboard</h1>
            </div>
            <div class="card-body">
                <!-- Alerts -->
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Welcome Message -->
                <p>Welcome, {{ Auth::user()->name }}! Your role is {{ Auth::user()->role }}.</p>

                <!-- Metrics Row -->
                <div class="row g-4 mb-4">
                    <!-- Open Problems -->
                    <div class="col-md-3">
                        <div class="card text-white bg-primary shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Open Problems</h5>
                                <p class="display-6">{{ $openProblems }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Assigned Problems -->
                    <div class="col-md-3">
                        <div class="card text-white bg-warning shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Assigned Problems</h5>
                                <p class="display-6">{{ $assignedProblems }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Resolved Problems -->
                    <div class="col-md-3">
                        <div class="card text-white bg-success shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Resolved Problems</h5>
                                <p class="display-6">{{ $resolvedProblems }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Unsolvable Problems -->
                    <div class="col-md-3">
                        <div class="card text-white bg-dark shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Unsolvable Problems</h5>
                                <p class="display-6">{{ $unsolvableProblems }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Average Resolution Time -->
                <div class="row g-4 mb-4">
                    <div class="col-md-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Average Resolution Time</h5>
                                <p class="display-6">
                                    {{ $avgResolutionTime ? number_format($avgResolutionTime, 2) . ' minutes' : 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row g-4">
                    <!-- Specialist Workload Chart -->
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Specialist Workload</h5>
                                <canvas id="workloadChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- Problem Status Distribution Chart -->
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Problem Status Distribution</h5>
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View Problems Button -->
                <div class="mt-4">
                    <a href="{{ route('problems.index') }}" class="btn btn-primary">View Problems</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Pass data to JavaScript -->
    <script>
        window.dashboardData = {
            specialistWorkloads: @json($specialistWorkloads),
            statusDistribution: @json($statusDistribution),
        };
    </script>
@endsection