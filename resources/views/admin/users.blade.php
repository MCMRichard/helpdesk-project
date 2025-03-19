@extends('layouts.app')

@section('content')
    <h1>Manage Users</h1>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>
                        <form action="{{ route('admin.users.updateRole', $user) }}" method="POST">
                            @csrf
                            <select name="role" class="form-control">
                                <option value="operator" {{ $user->role === 'operator' ? 'selected' : '' }}>Operator</option>
                                <option value="specialist" {{ $user->role === 'specialist' ? 'selected' : '' }}>Specialist</option>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection