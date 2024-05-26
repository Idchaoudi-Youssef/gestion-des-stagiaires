@extends('layouts.dashboard')

@section('main-content')
<div class="container">
    <h1>Create Task</h1>
    <form action="{{ route('supervisor.tasks.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" rows="5" required></textarea>
        </div>
        <div class="form-group">
            <label for="intern_id">Assign to Intern</label>
            <select name="intern_id" id="intern_id" class="form-control" required>
                @foreach($interns as $intern)
                    <option value="{{ $intern->id }}">{{ $intern->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Create Task</button>
    </form>
</div>
@endsection
