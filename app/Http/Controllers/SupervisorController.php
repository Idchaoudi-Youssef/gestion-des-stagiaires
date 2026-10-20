<?php

namespace App\Http\Controllers;

use App\Enums\TaskState;
use App\Enums\UserType;
use App\Models\Report;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskStateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupervisorController extends Controller
{
    public function __construct(private readonly TaskStateService $taskStates) {}

    /**
     * Statistics shown on the supervisor dashboard.
     */
    public function index(): View
    {
        $supervisorId = auth()->id();

        // The interns attached to this supervisor, with their tasks.
        $interns = User::where('usertype', UserType::Intern->value)
            ->whereHas('supervisors', fn ($query) => $query->where('supervisor_id', $supervisorId))
            ->with(['tasks' => fn ($query) => $query->select('id', 'title', 'state', 'intern_id')])
            ->get();

        $tasks = Task::where('supervisor_id', $supervisorId)->get();
        $this->taskStates->flagOverdueTasksIn($tasks);

        $unfinishedTasks = $tasks->where('state', TaskState::Unfinished)->count();
        $pendingTasks = $tasks->where('state', TaskState::Pending)->count();
        $completedTasks = $tasks->where('state', TaskState::Completed)->count();

        return view('supervisor.home', compact(
            'interns', 'unfinishedTasks', 'pendingTasks', 'completedTasks'
        ));
    }

    /**
     * List the tasks created by this supervisor.
     */
    public function viewTasks(): View
    {
        $tasks = Task::where('supervisor_id', auth()->id())->latest()->get();
        $this->taskStates->flagOverdueTasksIn($tasks);

        return view('supervisor.tasks.index', compact('tasks'));
    }

    /**
     * Form used to create a task.
     */
    public function createTask(): View
    {
        $interns = auth()->user()->interns()->orderBy('name')->get();

        return view('supervisor.tasks.create', compact('interns'));
    }

    /**
     * Create a task for one of the interns of this supervisor.
     */
    public function storeTask(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'limit_date' => ['required', 'date'],
            'intern_id' => ['required', 'exists:users,id'],
        ]);

        // The intern has to be one of the interns of this supervisor.
        $intern = auth()->user()->interns()->find($validated['intern_id']);

        if ($intern === null) {
            return redirect()
                ->route('supervisor.tasks.create')
                ->withErrors(['intern_id' => 'You can only give a task to one of your interns.']);
        }

        Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'intern_id' => $intern->id,
            'supervisor_id' => auth()->id(),
            'limit_date' => $validated['limit_date'],
            'state' => TaskState::Pending->value,
        ]);

        return redirect()
            ->route('supervisor.tasks')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Form used to edit a task.
     */
    public function editTask(Task $task): View
    {
        $this->authorize('update', $task);

        $interns = auth()->user()->interns()->orderBy('name')->get();

        return view('supervisor.tasks.edit', compact('task', 'interns'));
    }

    /**
     * Update a task.
     */
    public function updateTask(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'intern_id' => ['required', 'exists:users,id'],
            'limit_date' => ['required', 'date'],
        ]);

        $intern = auth()->user()->interns()->find($validated['intern_id']);

        if ($intern === null) {
            return redirect()
                ->route('supervisor.tasks.edit', $task)
                ->withErrors(['intern_id' => 'You can only give a task to one of your interns.']);
        }

        $task->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'intern_id' => $intern->id,
            'limit_date' => $validated['limit_date'],
            'state' => TaskState::Pending->value,
        ]);

        return redirect()
            ->route('supervisor.tasks')
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Delete a task.
     */
    public function destroyTask(Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()
            ->route('supervisor.tasks')
            ->with('success', 'Task deleted successfully.');
    }

    /**
     * Weekly reports sent to this supervisor.
     */
    public function viewWeeklyReports(): View
    {
        $weeklyReports = Report::where('type', 'weekly')
            ->where('supervisor_id', auth()->id())
            ->with('intern')
            ->latest()
            ->get();

        return view('supervisor.reports.weekly', compact('weeklyReports'));
    }
}
