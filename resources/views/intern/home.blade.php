@extends('layouts.dashboard')

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
        <!-- Time Left in Internship -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Time Left in Internship (Days)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @if ($timeLeft >= 0)
                                    {{ ceil($timeLeft) }}
                                @else
                                    Internship Ended
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unfinished Tasks -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Unfinished Tasks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $unfinishedTasks }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Tasks -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Tasks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingTasks }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Tasks -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed Tasks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedTasks }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card shadow">
            <div class="card-header" style="background-color: #eeeeee; color: #000000;">
                <h6 class="m-0 font-weight-bold">Interns in the Same Department</h6>
            </div>
                <div class="dropdown-list">
                    @foreach ($interns as $key => $intern)
                    @php
                        $colors = ['#E0FBE2' , '#FFFBDA', '#C0D6E8', '#F6D6D6'];
                        $bg_color = $colors[$key % count($colors)];
                        $text_color = '#000';
                    @endphp
                    <a class="dropdown-item d-flex align-items-center" style="background-color: {{ $bg_color }}; color: {{ $text_color }};">
                        <div class="dropdown-list-image mr-3">
                            <div class="rounded-circle placeholder text-center" style="width: 60px; height: 60px; background-color: {{ $key % 2 == 0 ? '#f8f9fa' : '#EAEAEA' }}; color: #495057; font-size: 24px; line-height: 60px;">{{ substr($intern->name, 0, 1) }}</div>
                        </div>
                        <div class="font-weight-bold">
                            <div class="text-truncate">{{ $intern->name }}</div>
                            <div class="small text-gray-700">{{ $intern->email }}</div>
                        </div>
                    </a>
                    @endforeach
            </div>
        </div>
    </div>


</div>
</div>
    <!-- Your Other Dashboard Components Here -->

@endsection
