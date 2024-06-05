@extends('layouts.dashboard')

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
   document.addEventListener("DOMContentLoaded", function() {
        // Reports Chart - Number of interns per month
        var ctx = document.getElementById('reportsChart').getContext('2d');
        var reportsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                datasets: [{
                    label: 'Number of Interns',
                    data: @json(array_values($internsData)),
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Intern Status Chart
        var ctx2 = document.getElementById('internStatusChart').getContext('2d');
        var internStatusChart = new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: @json($internStatusLabels),
                datasets: [{
                    label: 'Intern Status',
                    data: @json($internStatusData),
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(255, 99, 132, 0.2)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Users per Department Chart
        var ctx3 = document.getElementById('usersPerDepartmentChart').getContext('2d');
        var usersPerDepartmentChart = new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: @json(array_keys($usersPerDepartment->toArray())),
                datasets: [{
                    label: 'Number of Users',
                    data: @json(array_values($usersPerDepartment->toArray())),
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
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

        <!-- Interns Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Interns</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $internCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supervisors Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Supervisors</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $supervisorCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Reports</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $reportCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <!-- Reports Chart -->
        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="reportsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Intern Status Chart -->
        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="internStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users per Department Chart -->
        <div class="col-xl-6 col-md-12 mb-4" style="margin: auto;">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="usersPerDepartmentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
