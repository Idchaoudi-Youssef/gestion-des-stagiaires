@extends('layouts.dashboard')

@section('main-content')
<div class="container-fluid">

    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="h3 mb-0 text-gray-800">Tasks</h1>
        <a href="{{ route('supervisor.tasks.create') }}" class="btn btn-primary" style="margin-bottom: 10px ; font-size: 15px">Create Task</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" style="text-align: center">
                    <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Intern</th>
                    <th>state</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                    <tr>
                        <td>{{ $task->title }}</td>
                        <td>{{ $task->description }}</td>
                        <td>{{ $task->intern->name }}</td>
                        <td>
                                @php
                                    $stateColorClass = '';
                                    switch($task->state) {
                                        case 'Completed':
                                            $stateColorClass = 'bg-success'; // Green
                                            break;
                                        case 'Pending':
                                            $stateColorClass = 'bg-warning'; // Yellow
                                            break;
                                        case 'unfinished':
                                            $stateColorClass = 'bg-danger'; // Red
                                            break;

                                    }
                                @endphp
                                <div class="card {{ $stateColorClass }} text-white  " style="padding : 5px 5px 5px 5px ">
                                   {{ $task->state }}
                                </div>
                            </div>
                        </td>


                        <td>
                            <a href="{{ route('supervisor.tasks.edit', $task->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('supervisor.tasks.destroy', $task->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
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
