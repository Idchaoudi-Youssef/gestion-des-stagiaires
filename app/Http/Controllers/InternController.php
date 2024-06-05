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
use App\Models\Internship;

class InternController extends Controller
{

    public function index()
    {
        $user = User::find(Auth::id());
        // Assuming $user holds the current authenticated user
        $internship = $user->internship;

        // Calculate time left in the internship
        $endDate = Carbon::parse($internship->end_date);
        $now = Carbon::now();
        $timeLeft = $now->diffInDays($endDate, false); // Set the second parameter to false to get a negative value if the date is in the past

        // Number of tasks
        $completedTasks = $user->tasks()->where('state', 'completed')->count();
        $pendingTasks = $user->tasks()->where('state', 'pending')->count();
        $unfinishedTasks = $user->tasks()->where('state', 'unfinished')->count();

        // Fetch interns in the same department
        $interns = User::where('department_id', $user->department_id)->where('usertype', 'intern')->get();

        return view('intern/home', compact(
            'timeLeft', 'unfinishedTasks', 'pendingTasks', 'completedTasks', 'interns'
        ));
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
    public function addReport()
    {
        return view('intern.reports.add');
    }

    public function submitReport(Request $request)
{
    $rules = [
        'file' => 'required|file|mimes:pdf,doc,docx|max:2048',
        'type' => 'required|in:weekly,final',
    ];

    if ($request->type === 'weekly') {
        $rules['supervisor_id'] = 'required|exists:users,id';
    }

    $request->validate($rules);

    $intern = Auth::user();

    // Check if the type is weekly to find supervisor
    if ($request->type === 'weekly') {
        $supervisor = $intern->supervisors()->find($request->supervisor_id);

        if (!$supervisor) {
            return redirect()->route('intern.reports')->with('error', 'Selected supervisor is not valid.');
        }
    } else {
        $supervisor = null; // Set supervisor to null if type is final
    }

    $path = $request->file('file')->store('reports', 'public');

    $report = new Report();
    $report->content = ''; // No content, since we're using a file
    $report->type = $request->type;
    $report->intern_id = $intern->id;
    $report->supervisor_id = $supervisor ? $supervisor->id : null; // Set supervisor_id based on type
    $report->file = $path;

    if ($request->type == 'final') {
        $report->admin_id = 1; // Assuming admin_id 1 for simplicity
    }

    $report->save();

    return redirect()->route('intern.reports')->with('success', 'Report submitted successfully.');
}


    public function attestation()
    {
    $user = Auth::user();



    // Fetch the internship details
    $internship = Internship::where('intern_id', $user->id)->firstOrFail();
    // Signatory details (these could be hardcoded or fetched from a configuration or another model)
    $signatory_name = 'Nom du Signataire';
    $signatory_position = 'Poste du Signataire';

    // Company SIRET
    $company_siret = '000 000 000 00000';

    return view('intern.attestation', compact('user', 'internship', 'signatory_name', 'signatory_position', 'company_siret' ));
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
