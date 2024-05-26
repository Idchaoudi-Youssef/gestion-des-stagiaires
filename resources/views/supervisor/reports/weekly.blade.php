@extends('layouts.dashboard')

@section('main-content')
<div class="container">
    <h1>Weekly Reports</h1>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Intern Name</th>
                    <th>Report Content</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($weeklyReports as $report)
                    <tr>
                        <td>{{ $report->intern->name }}</td>
                        <td>{{ $report->content }}</td>
                        <td>{{ $report->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
