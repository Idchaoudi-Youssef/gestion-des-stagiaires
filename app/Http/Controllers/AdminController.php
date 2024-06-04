<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Department;
use App\Models\Internship;
use Carbon\Carbon;



class AdminController extends Controller
{
    public function index()
    {
        $internCount = User::where('usertype', 'intern')->count();
        $supervisorCount = User::where('usertype', 'supervisor')->count();
        $reportCount = Report::where('type', 'final')->count();
        Carbon::setLocale('Europe/London'); // GMT +1 during daylight saving time
        $today = Carbon::today();
        $internshipsEndingToday = Internship::whereDate('end_date', $today)->pluck('intern_id');

        // Update the status of interns whose internships are ending today to 'finished'
        User::whereIn('id', $internshipsEndingToday)->where('usertype', 'intern')->update(['status' => 'finished']);
        $internsPerMonth = User::selectRaw('COUNT(*) as count, MONTH(created_at) as month')
            ->where('usertype', 'intern')
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Fill in missing months with 0
        $internsData = array_fill(1, 12, 0);
        foreach ($internsPerMonth as $month => $count) {
            $internsData[$month] = $count;
        }

        // Get users per department
        $usersPerDepartment = User::selectRaw('departments.name as department, COUNT(users.id) as count')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->groupBy('departments.name')
            ->pluck('count', 'department');

        // Calculate interns by status
        $inPeriodCount = User::where('usertype', 'intern')->where('status', 'in_period')->count();
        $notStartedCount = User::where('usertype', 'intern')->where('status', 'not_started')->count();
        $finishedCount = User::where('usertype', 'intern')->where('status', 'finished')->count();

        // Data for the pie chart
        $internStatusData = [$inPeriodCount, $notStartedCount, $finishedCount];
        $internStatusLabels = ['In Period', 'Not Started', 'Finished'];

        return view('admin.home', compact(
            'internCount', 'supervisorCount', 'reportCount',
            'internsData', 'usersPerDepartment', 'internStatusData', 'internStatusLabels'
        ));
    }



    public function showAssignForm()
    {
        $interns = User::where('usertype', 'intern')->get();
        $supervisors = User::where('usertype', 'supervisor')->get();

        return view('admin.assign', compact('interns', 'supervisors'));
    }

    // Process the form to assign interns to supervisors
    public function assignInternsToSupervisors(Request $request)
    {
        $request->validate([
            'intern_id' => 'required|exists:users,id',
            'supervisor_ids' => 'required|array',
            'supervisor_ids.*' => 'exists:users,id',
        ]);

        $intern = User::find($request->intern_id);
        $intern->supervisors()->sync($request->supervisor_ids);

        return redirect()->route('admin.showAssignForm')->with('success', 'Intern assigned to supervisors successfully.');
    }
    public function getUsers()
    {
        $users = User::where('usertype', '!=', 'admin')->get();
        return view('admin/usersTable', compact('users'));
    }

    // public function editUser(User $user)
    // {
    //     // Ensure the user is not an admin
    //     if ($user->role == 'admin') {
    //         abort(403, 'Unauthorized action.');
    //     }
    //     $departments = Department::all();

    //     return view('admin/editUser', compact('user' , 'departments'));
    // }



    public function createUser()
    {
        $departments = Department::all();
        return view('admin/createUser', compact('departments'));
    }

    public function storeUser(Request $request)
    {
        //dd($request->all());
        $validatedData = $request->validate([
            'usertype' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|max:12',
            'department_id' => 'required|exists:departments,id',
        ]);

        try {
            $validatedData['password'] = bcrypt($validatedData['password']);
            $user = User::create($validatedData);
            return redirect()->route('admin.users')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'User creation failed. Please try again.']);
        }
    }


    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $departments = Department::all();
        return view('admin/editUser', compact('user', 'departments'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'usertype' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|max:12',
            'department_id' => 'required|exists:departments,id',
        ]);

        try {
            if ($request->filled('password')) {
                $validatedData['password'] = bcrypt($validatedData['password']);
            } else {
                unset($validatedData['password']);
            }

            $user->update($validatedData);

            return redirect()->route('admin.users')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'User update failed. Please try again.']);
        }
    }


    public function deleteUser(User $user)
    {
        try {
            // Ensure the user is not an admin
            if ($user->role == 'admin') {
                abort(403, 'Unauthorized action.');
            }

            $user->delete();
            return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'User deletion failed. Please try again.']);
        }
    }
    public function viewFinalReports()
    {
        // Fetch all final reports
        $finalReports = Report::where('type', 'final')->get();
        return view('admin.reports.reportsTable', compact('finalReports'));
    }
    public function createInternshipData(User $user)
    {
        // Ensure the user is an intern
        if ($user->usertype !== 'intern') {
            return redirect()->route('users.index')->withErrors(['error' => 'User is not an intern.']);
        }

        $departments = Department::all();
        return view('admin.intershipdata', compact('user', 'departments'));
    }

    public function storeInternshipData(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'internship_period' => 'required|integer',
        ]);

        try {
            // Create internship data
            Internship::create([
                'intern_id' => $user->id,
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
                'internship_period' => $validatedData['internship_period'],
            ]);

            // Update the user's status to 'in_period'
            $user->update(['status' => 'in_period']);

            $interns = User::where('usertype', 'intern')->get();
            return view('admin.internslist', compact('interns'))->with('success', 'Internship data added successfully.');
        } catch (\Exception $e) {
            // Log or dump the exception message for debugging
            dd($e->getMessage());

            // You can also redirect back with an error message
            // return redirect()->back()->withErrors(['error' => 'Failed to add internship data. Please try again.']);
        }
    }

    public function listInterns()
    {
        $interns = User::where('usertype', 'intern')->get();
        return view('admin.internslist', compact('interns'));
    }
    public function editInternshipData(Request $request, User $user)
    {
        // Fetch the internship data for the user
        $internship = $user->internship; // Assuming you have a relationship set up in the User model
        $departments = Department::all();

        // Pass the internship data and the user to the view
        return view('admin.editintershipdata', compact('internship', 'user' , 'departments'));
    }

    public function updateInternshipData(Request $request, User $user)
    {
        // Fetch the internship data for the user
        $internship = $user->internship; // Assuming you have a relationship set up in the User model

        // Validate the request data
        $validatedData = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'internship_period' => 'required|integer',
        ]);

        try {
            // Update the internship record with the validated data
            $internship->update($validatedData);
            $user->update(['status' => 'in_period']);

            $interns = User::where('usertype', 'intern')->get();
            return view('admin.internslist', compact('user', 'interns'));
        } catch (\Exception $e) {
            // Handle exceptions and redirect back with error message
            return redirect()->back()->withErrors(['error' => 'Failed to update internship data. Please try again.']);
        }
    }

    public function indexdep()
    {
        $departments = Department::all();
        return view('admin.departments.index', compact('departments'));
    }

    public function createdep()
    {
        return view('admin.departments.create');
    }

    public function storedep(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            // Add any other validation rules as necessary
        ]);

        Department::create($validatedData);

        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
    }

    public function editdep(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function updatedep(Request $request, Department $department)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            // Add any other validation rules as necessary
        ]);

        $department->update($validatedData);

        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroydep(Department $department)
    {
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
    }


}
