@extends('layouts.dashboard')

@section('main-content')
<div class="container">
    <h1>Add Department</h1>
    <form action="{{ route('departments.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter department name" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Department</button>
    </form>
</div>
@endsection
