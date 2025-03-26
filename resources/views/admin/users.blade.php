@extends('layouts.app')

@section('content')
    <h1>Manage Users</h1>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Specialties</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>
                        @if ($user->isSpecialist())
                            <form action="{{ route('admin.users.updateExpertise', $user) }}" method="POST">
                                @csrf
                                <select name="problem_type_ids[]" class="form-control @error('problem_type_ids') is-invalid @enderror" multiple>
                                    @foreach (\App\Models\ProblemType::all() as $type)
                                        <option value="{{ $type->problem_type_id }}"
                                            {{ $user->expertise->contains($type) ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('problem_type_ids')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message ?? 'Invalid input' }}</strong>
                                    </span>
                                @enderror
                                <button type="submit" class="btn btn-primary mt-2">Update Specialties</button>
                            </form>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('admin.users.updateRole', $user) }}" method="POST">
                            @csrf
                            <select name="role" class="form-control">
                                <option value="operator" {{ $user->role === 'operator' ? 'selected' : '' }}>Operator</option>
                                <option value="specialist" {{ $user->role === 'specialist' ? 'selected' : '' }}>Specialist</option>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            <button type="submit" class="btn btn-primary mt-2">Update Role</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection