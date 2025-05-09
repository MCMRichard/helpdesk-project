@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm" style="max-width: 600px; margin: auto;">
            <div class="card-header bg-dark text-white">
                <h1 class="h4 mb-0">Register</h1>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="password-confirm" class="form-label">Confirm Password</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                    </div>

                    <!-- Role -->
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                            <option value="operator" {{ old('role') === 'operator' ? 'selected' : '' }}>Operator</option>
                            <option value="specialist" {{ old('role') === 'specialist' ? 'selected' : '' }}>Specialist</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Specialties (for Specialists) -->
                    <div class="mb-3" id="specialties" style="display: {{ old('role') === 'specialist' ? 'block' : 'none' }};">
                        <label for="problem_type_ids" class="form-label">Specialties</label>
                        <select name="problem_type_ids[]" id="problem_type_ids" class="form-control" multiple>
                            @foreach (\App\Models\ProblemType::all() as $type)
                                <option value="{{ $type->problem_type_id }}" {{ in_array($type->problem_type_id, old('problem_type_ids', [])) ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('problem_type_ids')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary">Register</button>
                </form>

                <!-- JavaScript for Specialty Toggle -->
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const roleSelect = document.getElementById('role');
                        const specialtiesDiv = document.getElementById('specialties');
                        roleSelect.addEventListener('change', function () {
                            specialtiesDiv.style.display = this.value === 'specialist' ? 'block' : 'none';
                        });
                    });
                </script>
            </div>
        </div>
    </div>
@endsection