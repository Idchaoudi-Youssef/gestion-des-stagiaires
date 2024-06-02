@extends('layouts.dashboard')

@section('main-content')
<div id="content">
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-4 text-gray-800">Tasks</h1>

        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="row mb-4 justify-content-between">
            <form id="search-filter-form" action="{{ route('tasks.search') }}" method="GET" class="d-flex w-100">
                <div class="col-md-2 d-flex align-items-center justify-content-start">
                    <select name="supervisor" id="supervisor" class="form-control mr-2" onchange="document.getElementById('search-filter-form').submit();">
                        <option value="">All Supervisors</option>
                        @foreach($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}" {{ request('supervisor') == $supervisor->id ? 'selected' : '' }}>
                                {{ $supervisor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 ml-auto">
                    <!-- Combined Search and Filter Form -->
                    <div class="input-group">
                        <input type="text" name="query" class="form-control" placeholder="Search tasks..." value="{{ request('query') }}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit">Search</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>


        <div class="row" style="margin-bottom: 50px ;">
            <div class="col-md-10" style="margin: auto;">
                <div class="list-group">
                    <!-- Task Items -->
                    @foreach($tasks as $task)
                        <div class="list-group-item list-group-item-action flex-column align-items-start
                            @if($task->state == 'Pending') border-left-warning @elseif($task->state == 'Completed') border-left-success @else border-left-danger @endif">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ $task->title }}</h5>
                                <!-- Remaining Time -->
                                @if($task->state == 'Pending' && (!$task->limit_date || now()->isBefore(\Carbon\Carbon::parse($task->limit_date))))
                                    <small class="text-muted">
                                        @if($task->limit_date)
                                            @php
                                                $limitDate = \Carbon\Carbon::parse($task->limit_date);
                                                $now = \Carbon\Carbon::now();
                                                $diffInDays = floor($now->diffInHours($limitDate) / 24);
                                                $diffInHours = $now->diffInHours($limitDate) % 24;
                                                $diffInMinutes = $now->diffInMinutes($limitDate) % 60;
                                            @endphp
                                            {{ $diffInDays }}d {{ $diffInHours }}h {{ $diffInMinutes }}m left
                                        @endif
                                    </small>
                                @endif
                            </div>
                            <!-- Complete Task Button -->
                            @if($task->state == 'Pending' && (!$task->limit_date || now()->isBefore(\Carbon\Carbon::parse($task->limit_date))))
                                <div class="float-right">
                                    <form method="POST" action="{{ route('task.complete', $task->id) }}" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check"></i></button>
                                    </form>
                                </div>
                            @endif
                            <!-- Task Description -->
                            <p class="mb-1"> <b>{{ $task->description }}</b> </p>
                            <!-- Task State -->
                            <div class="d-flex w-100 justify-content-between">
                                <small class="text-muted">@if($task->state == 'Pending' ) <div class="badge badge-warning"> Pending </div>@elseif($task->state == 'Completed') <div class="badge badge-success"> Completed </div>@else <div class="badge badge-danger"> Unfinished </div>@endif</small>
                                <!-- Supervisor Name -->
                                <small class="text-muted " style="padding-top: 4px">From : {{ $task->supervisor->name }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
