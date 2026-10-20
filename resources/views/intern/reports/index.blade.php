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
                            @if($reports->contains(fn ($report) => $report->type !== \App\Enums\ReportType::Weekly))
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
                                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($report->file) }}" target="_blank" rel="noopener">View File</a>
                                    @else
                                        {{ $report->content }}
                                    @endif
                                </td>
                                <td>{{ $report->type->label() }}</td>
                                @if($report->type !== \App\Enums\ReportType::Weekly)
                                    <td>
                                        <span class="badge badge-{{ $report->state->badge() }}">{{ ucfirst($report->state->value) }}</span>
                                    </td>
                                    <td>
                                        @if($report->state === \App\Enums\ReportState::Rejected)
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
