<?php
namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;
class SupervisorController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        return view('supervisor/home');
    }

    public function viewTasks()
    {
        $tasks = Task::where('supervisor_id', Auth::id())->get();
        $this->updateTaskStates($tasks);

        return view('supervisor.tasks.index', compact('tasks'));
    }
    private function updateTaskStates($tasks)
    {
        // Set the default timezone for Carbon to GMT+1
        Carbon::setLocale('Europe/London'); // GMT +1 during daylight saving time

        foreach ($tasks as $task)
        {
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

    public function createTask()
    {
        // Get interns assigned to the logged-in supervisor
        $interns = Auth::user()->interns;
        return view('supervisor.tasks.create', compact('interns'));
    }





    public function storeTask(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'limit_date' => 'required|date_format:Y-m-d\TH:i',
            'intern_id' => 'required|exists:users,id',
        ]);

        // Check if the selected intern belongs to the logged-in supervisor
        $intern = User::find($request->intern_id);
        if (!$intern) {
            return redirect()->route('supervisor.tasks.create')->with('error', 'Unauthorized action.');
        }

        $task = new Task();
        $task->title = $request->title;
        $task->description = $request->description;
        $task->intern_id = $request->intern_id;
        $task->supervisor_id = Auth::id();
        $task->limit_date = Carbon::parse($request->limit_date);
        $task->state = 'Pending'; // Initialize the state
        $task->save();

        return redirect()->route('supervisor.tasks')->with('success', 'Task created successfully.');
    }



    public function editTask(Task $task)
    {
        $this->authorize('update', $task);

        // Get interns assigned to the logged-in supervisor
        $interns = Auth::user()->interns;
        return view('supervisor.tasks.edit', compact('task', 'interns'));
    }

    public function updateTask(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'intern_id' => 'required|exists:users,id',
            'limit_date' => 'required|date_format:Y-m-d\TH:i',
        ]);

        // Check if the selected intern belongs to the logged-in supervisor
        $intern = User::find($request->intern_id);
        if (!$intern) {
            return redirect()->route('supervisor.tasks.edit', $task->id)->with('error', 'Unauthorized action.');
        }

        $task->title = $request->title;
        $task->description = $request->description;
        $task->intern_id = $request->intern_id;
        $task->state = 'Pending';
        $task->limit_date = Carbon::parse($request->limit_date);

        

        $task->save();

        return redirect()->route('supervisor.tasks')->with('success', 'Task updated successfully.');
    }

    public function destroyTask(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();

        return redirect()->route('supervisor.tasks')->with('success', 'Task deleted successfully.');
    }

    public function viewWeeklyReports()
    {
        // Get weekly reports submitted to the logged-in supervisor
        $weeklyReports = Report::where('type', 'weekly')
                                ->where('supervisor_id', Auth::id())
                                ->with('intern') // Eager load the intern relationship
                                ->get();

        return view('supervisor.reports.weekly', compact('weeklyReports'));
    }
}
