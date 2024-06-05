@extends('layouts.dashboard')

@section('main-content')
<div class="container">
    <h1>List of Interns</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($interns as $intern)
                <tr>
                    <td>{{ $intern->name }}</td>
                    <td>{{ $intern->email }}</td>
                    <td>
                        @if($intern->internship->internship_period)
                            <a href="{{ route('interns.editInternshipDataForm', $intern->internship->intern_id) }}" class="btn btn-warning">Edit Internship</a>
                        @else
                            <a href="{{ route('interns.addInternshipDataForm', $intern->id) }}" class="btn btn-success">Add Internship</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
