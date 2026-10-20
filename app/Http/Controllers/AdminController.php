<?php

namespace App\Http\Controllers;

use App\Enums\InternshipStatus;
use App\Enums\ReportState;
use App\Enums\UserType;
use App\Models\Department;
use App\Models\Internship;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Statistics shown on the administrator dashboard.
     */
    public function index(): View
    {
        $internCount = User::where('usertype', UserType::Intern->value)->count();
        $supervisorCount = User::where('usertype', UserType::Supervisor->value)->count();
        $pendingCount = User::where('usertype', UserType::Pending->value)->count();
        $reportCount = Report::where('type', 'final')->count();

        // Number of interns created for each month of the current year.
        // The grouping is done in PHP so the query stays portable between
        // MySQL and SQLite.
        $internsPerMonth = User::query()
            ->where('usertype', UserType::Intern->value)
            ->whereYear('created_at', now()->year)
            ->get(['created_at'])
            ->groupBy(fn (User $user) => (int) $user->created_at->month)
            ->map(fn ($group) => $group->count());

        // Fill in the months without any intern so the chart keeps 12 points.
        $internsData = array_fill(1, 12, 0);
        foreach ($internsPerMonth as $month => $count) {
            $internsData[$month] = $count;
        }

        // Number of users per department.
        $usersPerDepartment = User::selectRaw('departments.name as department, COUNT(users.id) as count')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->groupBy('departments.name')
            ->pluck('count', 'department');

        // Interns sorted by internship status.
        $internStatusData = [
            Internship::where('status', InternshipStatus::InPeriod->value)->count(),
            Internship::where('status', InternshipStatus::NotStarted->value)->count(),
            Internship::where('status', InternshipStatus::Finished->value)->count(),
        ];
        $internStatusLabels = [
            InternshipStatus::InPeriod->label(),
            InternshipStatus::NotStarted->label(),
            InternshipStatus::Finished->label(),
        ];

        return view('admin.home', compact(
            'internCount', 'supervisorCount', 'pendingCount', 'reportCount',
            'internsData', 'usersPerDepartment', 'internStatusData', 'internStatusLabels'
        ));
    }

    /**
     * Approve a final report submitted by an intern.
     */
    public function approve(Request $request, Report $report): RedirectResponse
    {
        $report->update([
            'state' => ReportState::Approved->value,
            'admin_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.finalReports')
            ->with('success', 'Report approved successfully.');
    }

    /**
     * Reject a final report and keep the reason given to the intern.
     */
    public function reject(Request $request, Report $report): RedirectResponse
    {
        $request->validate([
            'reject_reason' => ['required', 'string', 'max:255'],
        ]);

        $report->update([
            'state' => ReportState::Rejected->value,
            'reject_reason' => $request->string('reject_reason')->toString(),
            'admin_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.finalReports')
            ->with('success', 'Report rejected successfully.');
    }

    /**
     * Delete every report that was rejected.
     */
    public function deleteRejectedReports(): RedirectResponse
    {
        $count = Report::where('state', ReportState::Rejected->value)->delete();

        return redirect()
            ->back()
            ->with('success', "{$count} rejected report(s) deleted successfully.");
    }

    /**
     * Form used to attach interns to supervisors.
     */
    public function showAssignForm(): View
    {
        $interns = User::where('usertype', UserType::Intern->value)->get();
        $supervisors = User::where('usertype', UserType::Supervisor->value)->get();

        return view('admin.assign', compact('interns', 'supervisors'));
    }

    /**
     * Attach an intern to one or more supervisors.
     */
    public function assignInternsToSupervisors(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'intern_id' => ['required', 'exists:users,id'],
            'supervisor_ids' => ['required', 'array', 'min:1'],
            'supervisor_ids.*' => ['exists:users,id'],
        ]);

        $intern = User::where('usertype', UserType::Intern->value)
            ->findOrFail($validated['intern_id']);

        $supervisors = User::where('usertype', UserType::Supervisor->value)
            ->whereIn('id', $validated['supervisor_ids'])
            ->get();

        if ($supervisors->count() !== count($validated['supervisor_ids'])) {
            throw ValidationException::withMessages([
                'supervisor_ids' => 'One of the selected users is not a supervisor.',
            ]);
        }

        $intern->supervisors()->sync($supervisors->pluck('id'));

        return redirect()
            ->route('admin.showAssignForm')
            ->with('success', 'Intern assigned to supervisors successfully.');
    }

    /**
     * List every account except the administrator ones.
     */
    public function getUsers(): View
    {
        $users = User::where('usertype', '!=', UserType::Admin->value)->orderBy('name')->get();

        return view('admin.usersTable', compact('users'));
    }

    /**
     * Form used to create a user.
     */
    public function createUser(): View
    {
        $departments = Department::orderBy('name')->get();

        return view('admin.createUser', compact('departments'));
    }

    /**
     * Create a user with a role chosen by the administrator.
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $this->validateUser($request);

        $user = User::create($validated);

        // The account was created by an administrator, so the address is
        // already trusted.
        $user->forceFill(['email_verified_at' => now()])->save();

        // An intern always needs an internship record, even before the
        // administrator fills in the dates.
        if ($user->usertype === UserType::Intern) {
            $user->internship()->create([
                'status' => InternshipStatus::NotStarted->value,
            ]);
        }

        return redirect()
            ->route('admin.users')
            ->with('success', 'User created successfully.');
    }

    /**
     * Form used to edit a user.
     */
    public function editUser(User $user): View
    {
        $departments = Department::orderBy('name')->get();

        return view('admin.editUser', compact('user', 'departments'));
    }

    /**
     * Update a user.
     */
    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $validated = $this->validateUser($request, $user);

        // Changing the email address invalidates the previous verification.
        $emailVerifiedAt = $validated['email'] !== $user->email
            ? null
            : $user->email_verified_at;

        $user->update($validated);
        $user->forceFill(['email_verified_at' => $emailVerifiedAt])->save();

        // Keep exactly one internship record per intern.
        if ($user->usertype === UserType::Intern && $user->internship === null) {
            $user->internship()->create([
                'status' => InternshipStatus::NotStarted->value,
            ]);
        }

        return redirect()
            ->route('admin.users')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Delete a user. Administrators are protected: another administrator has
     * to do it from the console.
     */
    public function deleteUser(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->id === $user->id) {
            return redirect()
                ->route('admin.users')
                ->withErrors(['error' => 'You cannot delete your own account here.']);
        }

        if ($user->usertype === UserType::Admin) {
            abort(403, 'An administrator account cannot be deleted from this screen.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Final reports waiting for a decision.
     */
    public function viewFinalReports(): View
    {
        $finalReports = Report::where('type', 'final')->latest()->get();

        return view('admin.reports.reportsTable', compact('finalReports'));
    }

    /**
     * Form used to attach the internship dates to an intern.
     */
    public function createInternshipData(User $user): View|RedirectResponse
    {
        if ($user->usertype !== UserType::Intern) {
            return redirect()
                ->route('admin.users')
                ->withErrors(['error' => 'This user is not an intern.']);
        }

        $departments = Department::orderBy('name')->get();

        return view('admin.intershipdata', compact('user', 'departments'));
    }

    /**
     * Save the internship dates of an intern.
     */
    public function storeInternshipData(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'position' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'internship_period' => ['required', 'integer', 'min:1', 'max:60'],
        ]);

        $user->internship()->updateOrCreate([], [
            'position' => $validated['position'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'internship_period' => $validated['internship_period'],
            'status' => InternshipStatus::InPeriod->value,
        ]);

        return redirect()
            ->route('interns.list')
            ->with('success', 'Internship data saved successfully.');
    }

    /**
     * List the interns.
     */
    public function listInterns(): View
    {
        $interns = User::where('usertype', UserType::Intern->value)->orderBy('name')->get();

        return view('admin.internslist', compact('interns'));
    }

    /**
     * Form used to edit the internship dates of an intern.
     */
    public function editInternshipData(User $user): View|RedirectResponse
    {
        if ($user->usertype !== UserType::Intern) {
            return redirect()
                ->route('admin.users')
                ->withErrors(['error' => 'This user is not an intern.']);
        }

        $internship = $user->internship;
        $departments = Department::orderBy('name')->get();

        return view('admin.editintershipdata', compact('internship', 'user', 'departments'));
    }

    /**
     * Update the internship dates of an intern.
     */
    public function updateInternshipData(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'position' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'internship_period' => ['required', 'integer', 'min:1', 'max:60'],
        ]);

        $user->internship()->updateOrCreate([], [
            'position' => $validated['position'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'internship_period' => $validated['internship_period'],
            'status' => InternshipStatus::InPeriod->value,
        ]);

        return redirect()
            ->route('interns.list')
            ->with('success', 'Internship data updated successfully.');
    }

    /**
     * List the departments.
     */
    public function indexdep(): View
    {
        $departments = Department::orderBy('name')->get();

        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Form used to create a department.
     */
    public function createdep(): View
    {
        return view('admin.departments.create');
    }

    /**
     * Create a department.
     */
    public function storedep(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name'],
        ]);

        Department::create($validated);

        return redirect()
            ->route('departments.index')
            ->with('success', 'Department created successfully.');
    }

    /**
     * Form used to edit a department.
     */
    public function editdep(Department $department): View
    {
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Update a department.
     */
    public function updatedep(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('departments', 'name')->ignore($department->id)],
        ]);

        $department->update($validated);

        return redirect()
            ->route('departments.index')
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Delete a department. The users of the department are kept, they simply
     * stop being attached to it.
     */
    public function destroydep(Department $department): RedirectResponse
    {
        $department->delete();

        return redirect()
            ->route('departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    /**
     * Validation rules shared by the create and the update form.
     *
     * @return array<string, mixed>
     */
    protected function validateUser(Request $request, ?User $user = null): array
    {
        $validated = $request->validate([
            'usertype' => ['required', Rule::in(UserType::assignable())],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(User::class, 'email')->ignore($user?->id),
            ],
            'password' => [$user === null ? 'required' : 'nullable', 'min:8', 'max:12'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ]);

        // Interns and supervisors always belong to a department.
        if (in_array($validated['usertype'], [UserType::Intern->value, UserType::Supervisor->value], true)
            && empty($validated['department_id'])) {
            throw ValidationException::withMessages([
                'department_id' => 'A department is required for interns and supervisors.',
            ]);
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        return $validated;
    }
}
