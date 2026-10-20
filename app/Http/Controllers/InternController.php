<?php

namespace App\Http\Controllers;

use App\Enums\ReportState;
use App\Enums\ReportType;
use App\Enums\TaskState;
use App\Models\Report;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskStateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InternController extends Controller
{
    public function __construct(private readonly TaskStateService $taskStates) {}

    /**
     * Statistics shown on the intern dashboard.
     */
    public function index(): View
    {
        $user = auth()->user();
        $internship = $user->internship;

        // Number of days left before the end of the internship. When the
        // dates are not known yet the internship is simply not started.
        $timeLeft = $internship?->end_date !== null
            ? now()->diffInDays($internship->end_date, false)
            : null;

        $tasks = $user->tasks()->get();
        $this->taskStates->flagOverdueTasksIn($tasks);

        $completedTasks = $tasks->where('state', TaskState::Completed)->count();
        $pendingTasks = $tasks->where('state', TaskState::Pending)->count();
        $unfinishedTasks = $tasks->where('state', TaskState::Unfinished)->count();

        // The other interns of the same department.
        $interns = User::where('department_id', $user->department_id)
            ->where('usertype', 'intern')
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get();

        return view('intern.home', compact(
            'timeLeft', 'unfinishedTasks', 'pendingTasks', 'completedTasks', 'interns'
        ));
    }

    /**
     * The tasks given to the logged in intern.
     */
    public function viewTasks(): View
    {
        $tasks = Task::where('intern_id', auth()->id())->latest()->get();
        $this->taskStates->flagOverdueTasksIn($tasks);

        $supervisors = auth()->user()->supervisors()->orderBy('name')->get();

        return view('intern.tasks.index', compact('tasks', 'supervisors'));
    }

    /**
     * The reports sent by the logged in intern.
     */
    public function viewReports(): View
    {
        $reports = Report::where('intern_id', auth()->id())
            ->with('supervisor')
            ->latest()
            ->get();

        return view('intern.reports.index', compact('reports'));
    }

    /**
     * Form used to submit a report.
     */
    public function addReport(): View
    {
        return view('intern.reports.add');
    }

    /**
     * Store a report uploaded by the intern.
     */
    public function submitReport(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:2048'],
            'type' => ['required', Rule::in([ReportType::Weekly->value, ReportType::Final->value])],
            'supervisor_id' => ['nullable', 'exists:users,id'],
        ]);

        $type = ReportType::from($validated['type']);
        $supervisor = null;

        // A weekly report has to be addressed to one of the supervisors of
        // the intern.
        if ($type === ReportType::Weekly) {
            $supervisor = auth()->user()->supervisors()->find($validated['supervisor_id'] ?? null);

            if ($supervisor === null) {
                return redirect()
                    ->route('intern.reports')
                    ->withErrors(['supervisor_id' => 'Please choose one of your supervisors.']);
            }
        }

        $path = $request->file('file')->store('reports', 'public');

        Report::create([
            'content' => '',
            'type' => $type->value,
            'intern_id' => auth()->id(),
            'supervisor_id' => $supervisor?->id,
            'file' => $path,
            'state' => ReportState::Pending->value,
        ]);

        return redirect()
            ->route('intern.reports')
            ->with('success', 'Report submitted successfully.');
    }

    /**
     * The internship certificate of the logged in intern.
     */
    public function attestation(): View
    {
        $user = auth()->user();
        $internship = $user->internship;

        $report = $user->reports()
            ->where('type', ReportType::Final->value)
            ->latest()
            ->first();

        return view('intern.attestation', [
            'user' => $user,
            'internship' => $internship,
            'report' => $report,
        ]);
    }

    /**
     * Mark a task as completed. Only the intern the task belongs to can do
     * it.
     */
    public function completeTask(Task $task): RedirectResponse
    {
        $this->authorize('complete', $task);

        $task->update(['state' => TaskState::Completed->value]);

        return redirect()
            ->back()
            ->with('success', 'Task marked as completed successfully.');
    }

    /**
     * Filter the tasks of the logged in intern by supervisor.
     */
    public function filter(Request $request): View
    {
        $supervisorId = $request->input('supervisor');

        $tasks = Task::where('intern_id', auth()->id())
            ->when($supervisorId, fn ($query) => $query->where('supervisor_id', $supervisorId))
            ->latest()
            ->get();

        $this->taskStates->flagOverdueTasksIn($tasks);

        $supervisors = auth()->user()->supervisors()->orderBy('name')->get();

        return view('intern.tasks.index', compact('tasks', 'supervisors'));
    }

    /**
     * Search inside the tasks of the logged in intern.
     */
    public function search(Request $request): View
    {
        $query = $request->input('query');
        $supervisorId = $request->input('supervisor');

        $tasks = Task::where('intern_id', auth()->id())
            ->when($query, function ($builder) use ($query) {
                $builder->where(function ($nested) use ($query) {
                    $nested->where('title', 'like', '%'.$query.'%')
                        ->orWhere('description', 'like', '%'.$query.'%');
                });
            })
            ->when($supervisorId, fn ($builder) => $builder->where('supervisor_id', $supervisorId))
            ->latest()
            ->get();

        $this->taskStates->flagOverdueTasksIn($tasks);

        $supervisors = auth()->user()->supervisors()->orderBy('name')->get();

        return view('intern.tasks.index', compact('tasks', 'supervisors'));
    }
}
