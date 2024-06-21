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
                    <option value="{{ $intern->id }}" data-department="{{ $intern->department_id }}">{{ $intern->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="supervisor_ids">Select Supervisors</label>
            <select name="supervisor_ids[]" id="supervisor_ids" class="form-control" multiple required>
                @foreach($supervisors as $supervisor)
                    <option value="{{ $supervisor->id }}" data-department="{{ $supervisor->department_id }}">{{ $supervisor->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Assign</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const internSelect = document.getElementById('intern_id');
    const supervisorSelect = document.getElementById('supervisor_ids');
    const allSupervisors = Array.from(supervisorSelect.options);

    internSelect.addEventListener('change', function () {
        const selectedInternDepartment = internSelect.selectedOptions[0].getAttribute('data-department');

        // Clear the current supervisor options
        supervisorSelect.innerHTML = '';

        // Filter supervisors based on the selected intern's department
        allSupervisors.forEach(option => {
            if (option.getAttribute('data-department') === selectedInternDepartment) {
                supervisorSelect.appendChild(option);
            }
        });
    });

    // Trigger change event to populate supervisors initially
    internSelect.dispatchEvent(new Event('change'));
});
</script>
@endsection
