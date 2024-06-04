@extends('layouts.dashboard')

@section('main-content')
    <div class="container">
        <h1>Edit Internship Data</h1>

        <form action="{{ route('interns.updateInternshipData', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $internship->start_date) }}" required>
                @error('start_date')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $internship->end_date) }}" required>
                @error('end_date')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            {{-- <div class="form-group">
                <label for="department_id">Department</label>
                <select name="department_id" class="form-control" required>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id', $internship->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                    @endforeach
                </select>
                @error('department_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div> --}}
            <div class="form-group">
                <label for="internship_period">Internship Period (Months)</label>
                <input type="number" name="internship_period" class="form-control" value="{{ old('internship_period', $internship->internship_period) }}" required>
                @error('internship_period')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection
