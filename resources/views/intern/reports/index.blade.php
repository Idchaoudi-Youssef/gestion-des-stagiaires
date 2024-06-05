@extends('layouts.dashboard')

@section('main-content')

<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <h1 class="h3 mb-0 text-gray-800" style="margin-bottom: 102px;">My Reports</h1>
    </div>
    <div>
        <a href="{{ route('intern.reports.create') }}" class="btn btn-primary" style="margin-bottom: 10px; font-size: 15px">Add Report</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" style="text-align: center">
                    <thead>
                        <tr>
                            <th>Report Content</th>
                            <th>Type</th>
                            @if(!empty($reports->where('type', '!=', 'weekly')->first()))
                                <th>Status</th>
                                <th>Rejection Reason</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            <tr>
                                <td>
                                    @if($report->file)
                                        <a href="{{ Storage::url($report->file) }}" target="_blank">View File</a>
                                    @else
                                        {{ $report->content }}
                                    @endif
                                </td>
                                <td>{{ ucfirst($report->type) }}</td>
                                @if($report->type != 'weekly')
                                    <td>
                                        @if ($report->state == 'approved')
                                            <span class="badge badge-success">{{ ucfirst($report->state) }}</span>
                                        @elseif ($report->state == 'rejected')
                                            <span class="badge badge-danger">{{ ucfirst($report->state) }}</span>
                                        @else
                                            <span class="badge badge-warning">{{ ucfirst($report->state) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($report->state == 'rejected')
                                            {{ $report->reject_reason }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection
