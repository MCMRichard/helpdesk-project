@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h1 class="h4 mb-0">Dashboard</h1>
            </div>
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <p>Welcome, {{ Auth::user()->name }}! Your role is {{ Auth::user()->role }}.</p>
                <a href="{{ route('problems.index') }}" class="btn btn-primary">View Problems</a>
            </div>
        </div>
    </div>
@endsection