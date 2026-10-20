@extends('layouts.dashboard')

@section('main-content')

<div class="container-fluid">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <h1 class="h3 mb-0 text-gray-800" style="margin-bottom: 102px;">Final Reports</h1>
        <form method="post" action="{{ route('delete.rejected.reports') }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-danger">Delete All Rejected Reports</button>
        </form>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" style="text-align: center">
                    <thead>
                        <tr>
                            <th>Intern Name</th>
                            <th>Report Content</th>
                            <th>Submitted At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($finalReports as $report)
                            <tr>
                                <td>{{ $report->intern->name }}</td>
                                <td>
                                    @if($report->file)
                                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($report->file) }}" target="_blank" rel="noopener">View File</a>
                                    @else
                                        {{ $report->content }}
                                    @endif
                                </td>
                                <td>{{ $report->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    @if($report->state === \App\Enums\ReportState::Pending)
                                        <form method="post" action="{{ route('approve.report', ['report' => $report->id]) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success">Approve</button>
                                        </form>
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal{{ $report->id }}">
                                            Reject
                                        </button>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal{{ $report->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel{{ $report->id }}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="rejectModalLabel{{ $report->id }}">Reject Report</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form method="post" action="{{ route('reject.report', ['report' => $report->id]) }}">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="reject_reason">Reason for Rejection</label>
                                                                <textarea class="form-control" id="reject_reason" name="reject_reason" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-danger">Reject</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge badge-{{ $report->state->badge() }}">{{ ucfirst($report->state->value) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection
