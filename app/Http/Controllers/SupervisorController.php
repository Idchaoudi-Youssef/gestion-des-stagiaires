<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class SupervisorController extends Controller
{
    use AuthorizesRequests;

    // Method to view tasks assigned by the supervisor
    public function viewTasks()
    {
        $tasks = Task::where('supervisor_id', Auth::id())->get();
        return view('supervisor.tasks.index', compact('tasks'));
    }

    // Method to show the form for creating a new task
    public function createTask()
    {
        $interns = User::where('usertype', 'intern')->get();
        return view('supervisor.tasks.create', compact('interns'));
    }

    // Method to store a newly created task
    public function storeTask(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'intern_id' => 'required|exists:users,id',
        ]);

        $task = new Task();
        $task->title = $request->title;
        $task->description = $request->description;
        $task->intern_id = $request->intern_id;
        $task->supervisor_id = Auth::id();
        $task->save();

        return redirect()->route('supervisor.tasks')->with('success', 'Task created successfully.');
    }

    // Method to show the form for editing a task
    public function editTask(Task $task)
    {
        $this->authorize('update', $task);
        $interns = User::where('usertype', 'intern')->get();
        return view('supervisor.tasks.edit', compact('task', 'interns'));
    }

    // Method to update an existing task
    public function updateTask(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'intern_id' => 'required|exists:users,id',
        ]);

        $task->title = $request->title;
        $task->description = $request->description;
        $task->intern_id = $request->intern_id;
        $task->save();

        return redirect()->route('supervisor.tasks')->with('success', 'Task updated successfully.');
    }

    // Method to delete a task
    public function destroyTask(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();

        return redirect()->route('supervisor.tasks')->with('success', 'Task deleted successfully.');
    }

    // Method to view weekly reports from interns
    public function viewWeeklyReports()
    {
        $weeklyReports = Report::where('type', 'weekly')
                                ->where('supervisor_id', Auth::id())
                                ->get();
        return view('supervisor.reports.weekly', compact('weeklyReports'));
    }
}
