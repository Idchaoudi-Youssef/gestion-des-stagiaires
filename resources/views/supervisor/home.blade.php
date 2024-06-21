@extends('layouts.dashboard')

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctx = document.getElementById('tasksStatusChart').getContext('2d');
        var tasksStatusChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Unfinished', 'Pending', 'Completed'],
                datasets: [{
                    label: 'Tasks',
                    data: [{{ $unfinishedTasks }}, {{ $pendingTasks }}, {{ $completedTasks }}],
                    backgroundColor: ['#FF6384', '#FFCE56', '#36A2EB'],
                    hoverBackgroundColor: ['#FF6384', '#FFCE56', '#36A2EB']
                }]
            },
            options: {
                responsive: true,
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Tasks by Status'
                }
            }
        });
    });
</script>
@endsection

@section('main-content')
<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800">{{ __('Dashboard') }}</h1>

@if (session('success'))
<div class="alert alert-success border-left-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if (session('status'))
    <div class="alert alert-success border-left-success" role="alert">
        {{ session('status') }}
    </div>
@endif



<div class="row">
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-s font-weight-bold text-info text-uppercase mb-1">Assigned Interns and Their Task Status</div>
                        <div class="mt-3">
                            <ul class="list-unstyled">
                                @foreach($interns as $intern)
                                <li class="mb-3">
                                    <strong>{{ $intern->name }}</strong>
                                    <span class="ml-3 badge badge-pill badge-danger">Unfinished - {{ $intern->tasks->where('state', 'unfinished')->count() }}</span>
                                    <span class="ml-3 badge badge-pill badge-warning">Pending - {{ $intern->tasks->where('state', 'Pending')->count() }}</span>
                                    <span class="ml-3 badge badge-pill badge-success">Completed - {{ $intern->tasks->where('state', 'Completed')->count() }}</span>
                                </li>
                                @if(!$loop->last)
                                <hr>
                                @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Tasks by Status Chart -->
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card shadow">
            <div class="card-header" style="background-color: #eeeeee; color: #000000;">
                <h6 class="m-0 font-weight-bold">Tasks by Status</h6>
            </div>
            <div class="card-body" style="width: 60%; margin: auto">
                <canvas id="tasksStatusChart"></canvas>
            </div>
        </div>
    </div>
</div>

@endsection
