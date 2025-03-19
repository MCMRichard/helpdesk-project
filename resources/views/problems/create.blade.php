@extends('layouts.app')
@section('content')
    <h1>Log New Problem</h1>
    <form action="{{ route('problems.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="caller_id">Caller</label>
            <select name="caller_id" class="form-control" required>
                @foreach ($callers as $caller)
                    <option value="{{ $caller->caller_id }}">{{ $caller->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="problem_type_id">Problem Type</label>
            <select name="problem_type_id" class="form-control" required>
                @foreach ($problemTypes as $type)
                    <option value="{{ $type->problem_type_id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="equipment_serial">Equipment Serial</label>
            <select name="equipment_serial" class="form-control">
                <option value="">None</option>
                @foreach ($equipment as $item)
                    <option value="{{ $item->serial_number }}">{{ $item->serial_number }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="software_id">Software</label>
            <select name="software_id" class="form-control">
                <option value="">None</option>
                @foreach ($software as $soft)
                    <option value="{{ $soft->software_id }}">{{ $soft->name }} ({{ $soft->version }})</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea name="notes" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
@endsection