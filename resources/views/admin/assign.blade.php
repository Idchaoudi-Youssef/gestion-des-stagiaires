@extends('layouts.dashboard')

@section('main-content')
<div class="container">
    <h1>Assign Interns to Supervisors</h1>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <form action="{{ route('admin.assignInternsToSupervisors') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="intern_id">Select Intern</label>
            <select name="intern_id" id="intern_id" class="form-control" required>
                @foreach($interns as $intern)
                    <option value="{{ $intern->id }}">{{ $intern->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="supervisor_ids">Select Supervisors</label>
            <select name="supervisor_ids[]" id="supervisor_ids" class="form-control" multiple required>
                @foreach($supervisors as $supervisor)
                    <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Assign</button>
    </form>
</div>
@endsection
