@extends('layouts.dashboard')

@section('main-content')
<div class="container">

    <h1>Add Internship Data for {{ $user->name }}</h1>

    <form action="{{ route('interns.addInternshipDataForm', $user->id) }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="internship_period">Internship Period</label>
            <div class="input-group">
                <input type="number" name="internship_period" class="form-control" value="{{ old('internship_period') }}" required>
                <div class="input-group-append">
                    <span class="input-group-text">months</span>
                </div>
            </div>

            @error('internship_period')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>


        <div class="form-group">
            <label for="start_date">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
            @error('start_date')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="end_date">End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
            @error('end_date')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        {{-- <div class="form-group">
            <label for="department_id">Department</label>
            <select name="department_id" class="form-control" required>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
            @error('department_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div> --}}

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
@endsection
