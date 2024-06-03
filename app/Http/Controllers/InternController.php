<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class InternController extends Controller
{
    public function index()
    {
        return view('intern/home');
    }

    public function viewTasks()
    {
        $tasks = Task::where('intern_id', Auth::id())->get();

        // Update tasks with passed deadlines before displaying
        $this->updateTaskStates($tasks);

        // Retrieve supervisors for the dropdown menu
        $supervisors = User::where('usertype', 'supervisor')->get();

        return view('intern.tasks.index', compact('tasks', 'supervisors'));
    }

    private function updateTaskStates($tasks)
    {
        // Set the default timezone for Carbon to GMT+1
        Carbon::setLocale('Europe/London'); // GMT +1 during daylight saving time

        foreach ($tasks as $task) {
            if ($task->limit_date) {
                // Parse the limit_date with the GMT+1 timezone
                $parsedDate = Carbon::parse($task->limit_date, 'Europe/London');

                // Ensure the comparison is done in the same timezone
                if ($parsedDate->lessThan(Carbon::now('Europe/London'))) {
                    $task->state = 'unfinished';
                    $task->save();
                }
            }
        }
    }

    public function viewReports()
    {
        $reports = Report::where('intern_id', Auth::id())->with('supervisor')->get();
        return view('intern.reports.index', compact('reports'));
    }

    public function submitReport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'type' => 'required|in:weekly,final',
            'supervisor_id' => 'required|exists:users,id',
        ]);

        $intern = Auth::user();
        $supervisor = $intern->supervisors()->find($request->supervisor_id);

        if (!$supervisor) {
            return redirect()->route('intern.reports')->with('error', 'Selected supervisor is not valid.');
        }

        $path = $request->file('file')->store('reports', 'public');

        $report = new Report();
        $report->content = ''; // No content, since we're using a file
        $report->type = $request->type;
        $report->intern_id = $intern->id;
        $report->supervisor_id = $supervisor->id;
        $report->file = $path;

        if ($request->type == 'final') {
            $report->admin_id = 1; // Assuming admin_id 1 for simplicity
        }

        $report->save();

        return redirect()->route('intern.reports')->with('success', 'Report submitted successfully.');
    }

    public function attestation()
    {
        $intern = Auth::user();
        return view('intern.attestation', compact('intern'));
    }


    public function completeTask(Task $task)
    {
        // Mark the task as completed
        $task->state = 'Completed';
        $task->save();

        // Redirect back with success message
        return redirect()->back()->with('success', 'Task marked as completed successfully.');
    }

    public function filter(Request $request)
    {
        // Retrieve the selected supervisor ID from the request
        $supervisorId = $request->input('supervisor');

        // If no supervisor is selected, retrieve tasks for the logged-in intern
        if (!$supervisorId) {
            $tasks = Task::where('intern_id', Auth::id())->get();
        } else {
            // Filter tasks by the selected supervisor and the logged-in intern
            $tasks = Task::where('supervisor_id', $supervisorId)
                         ->where('intern_id', Auth::id())
                         ->get();
        }

        // Retrieve supervisors for the dropdown menu
        $supervisors = User::where('usertype', 'supervisor')->get();

        // Pass the $supervisors variable to the view
        return view('intern.tasks.index', compact('tasks', 'supervisors'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $supervisorId = $request->input('supervisor');

        // Perform search and filter logic to get tasks based on the query and supervisor
        $tasksQuery = Task::query();

        if ($query) {
            $tasksQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', '%'.$query.'%')
                  ->orWhere('description', 'like', '%'.$query.'%')
                  ->orWhere('state', 'like', '%'.$query.'%');
            });
        }

        if ($supervisorId) {
            $tasksQuery->where('supervisor_id', $supervisorId);
        }

        $tasks = $tasksQuery->get();

        // Retrieve supervisors for the dropdown menu
        $supervisors = User::where('usertype', 'supervisor')->get();

        return view('intern.tasks.index', compact('tasks', 'supervisors'));
    }

}
