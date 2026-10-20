@extends('layouts.dashboard')

@section('main-content')
<div class="container">
    <h1>Edit Task</h1>
    <form action="{{ route('supervisor.tasks.update', $task) }}" method="POST">
        @csrf
        @method('PUT')
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $task->title) }}" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" rows="5" required>{{ old('description', $task->description) }}</textarea>
        </div>
        <div class="form-group">
            <label for="limit_date">Limit Date</label>
            <input type="datetime-local" name="limit_date" id="limit_date" class="form-control"
                   value="{{ old('limit_date', $task->limit_date?->format('Y-m-d\TH:i')) }}" required>
        </div>
        <div class="form-group">
            <label for="intern_id">Assign to Intern</label>
            <select name="intern_id" id="intern_id" class="form-control" required>
                @foreach($interns as $intern)
                    <option value="{{ $intern->id }}" @if($intern->id == $task->intern_id) selected @endif>{{ $intern->name }}</option>
                @endforeach
            </select>
            @error('intern_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Update Task</button>
    </form>
</div>
@endsection
