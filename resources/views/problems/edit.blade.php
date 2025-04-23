@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h1 class="h4 mb-0">Edit Problem #{{ $problem->problem_number }}</h1>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('problems.update', $problem->problem_number) }}">
                    @csrf
                    @method('PUT')

                    <!-- Caller -->
                    <div class="mb-3">
                        <label for="caller_id" class="form-label">Caller</label>
                        <select id="caller_id" name="caller_id" class="form-control @error('caller_id') is-invalid @enderror" required>
                            <option value="">Select Caller</option>
                            @foreach ($callers as $caller)
                                <option value="{{ $caller->caller_id }}" {{ old('caller_id', $problem->caller_id) == $caller->caller_id ? 'selected' : '' }}>
                                    {{ $caller->name }} ({{ $caller->department }})
                                </option>
                            @endforeach
                        </select>
                        @error('caller_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Problem Type -->
                    <div class="mb-3">
                        <label for="problem_type_id" class="form-label">Problem Type</label>
                        <select id="problem_type_id" name="problem_type_id" class="form-control @error('problem_type_id') is-invalid @enderror" required>
                            <option value="">Select Problem Type</option>
                            @foreach ($problemTypes as $type)
                                <option value="{{ $type->problem_type_id }}" {{ old('problem_type_id', $problem->problem_type_id) == $type->problem_type_id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('problem_type_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Equipment -->
                    <div class="mb-3">
                        <label for="equipment_serial" class="form-label">Equipment (Optional)</label>
                        <select id="equipment_serial" name="equipment_serial" class="form-control @error('equipment_serial') is-invalid @enderror">
                            <option value="">None</option>
                            @foreach ($equipment as $item)
                                <option value="{{ $item->serial_number }}" {{ old('equipment_serial', $problem->equipment_serial) == $item->serial_number ? 'selected' : '' }}>
                                    {{ $item->type }} - {{ $item->serial_number }}
                                </option>
                            @endforeach
                        </select>
                        @error('equipment_serial')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Software -->
                    <div class="mb-3">
                        <label for="software_id" class="form-label">Software (Optional)</label>
                        <select id="software_id" name="software_id" class="form-control @error('software_id') is-invalid @enderror">
                            <option value="">None</option>
                            @foreach ($software as $soft)
                                <option value="{{ $soft->software_id }}" {{ old('software_id', $problem->software_id) == $soft->software_id ? 'selected' : '' }}>
                                    {{ $soft->name }} ({{ $soft->version }})
                                </option>
                            @endforeach
                        </select>
                        @error('software_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="4">{{ old('notes', $problem->notes) }}</textarea>
                        @error('notes')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary">Update Problem</button>
                    <a href="{{ route('problems.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection