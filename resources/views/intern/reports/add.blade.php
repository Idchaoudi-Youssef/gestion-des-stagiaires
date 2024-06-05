@extends('layouts.dashboard')

@section('main-content')
<div class="container">
    <h1> Add Reports</h1>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('intern.submitReport') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="file">File</label>
            <input type="file" name="file" id="file" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="type">Type</label>
            <select name="type" id="type" class="form-control" required>
                <option value="weekly">Weekly</option>
                <option value="final">Final</option>
            </select>
        </div>
        <div class="form-group" id="supervisor-select-group">
            <label for="supervisor_id">Select Supervisor</label>
            <select name="supervisor_id" id="supervisor_id" class="form-control">
                @foreach(Auth::user()->supervisors as $supervisor)
                    <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Submit Report</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var typeSelect = document.getElementById('type');
        var supervisorSelectGroup = document.getElementById('supervisor-select-group');

        function toggleSupervisorSelect() {
            if (typeSelect.value === 'final') {
                supervisorSelectGroup.style.display = 'none';
                document.getElementById('supervisor_id').required = false;
            } else {
                supervisorSelectGroup.style.display = 'block';
                document.getElementById('supervisor_id').required = true;
            }
        }

        typeSelect.addEventListener('change', toggleSupervisorSelect);
        toggleSupervisorSelect(); // Initial call to set the correct state on page load
    });
</script>
@endsection
