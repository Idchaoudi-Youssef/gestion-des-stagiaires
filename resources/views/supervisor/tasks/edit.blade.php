@extends('layouts.dashboard')

@section('main-content')
<div class="container">
    <h1>Edit Task</h1>
    <form action="{{ route('supervisor.tasks.update', $task->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ $task->title }}" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" rows="5" required>{{ $task->description }}</textarea>
        </div>
        <div class="form-group">
            <label for="limit_date">Limit Date</label>
            <input type="datetime-local" name="limit_date" id="limit_date" class="form-control" required >
        </div>
        <div class="form-group">
            <label for="intern_id">Assign to Intern</label>
            <select name="intern_id" id="intern_id" class="form-control" required>
                @foreach($interns as $intern)
                    <option value="{{ $intern->id }}" @if($intern->id == $task->intern_id) selected @endif>{{ $intern->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Task</button>
    </form>
</div>
@endsection
