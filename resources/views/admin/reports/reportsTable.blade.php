@extends('layouts.dashboard')

@section('main-content')

<div class="container-fluid">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px ;">
        <h1 class="h3 mb-0 text-gray-800" style="margin-bottom: 102px ;">Final Reports</h1>
    </div>


    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" style="text-align: center">
                    <thead>
                        <tr>
                            <th>Intern Name</th>
                            <th>Supervisor Name</th>
                            <th>Report Content</th>
                            <th>Submitted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($finalReports as $report)
                            <tr>
                                <td>{{ $report->intern->name }}</td>
                                <td>{{ $report->supervisor ? $report->supervisor->name : 'N/A' }}</td>
                                <td> @if($report->file)
                                    <a href="{{ Storage::url($report->file) }}" target="_blank">View File</a>
                                @else
                                    {{ $report->content }}
                                @endif</td>
                                <td>{{ $report->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection
