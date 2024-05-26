<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Report;

class AdminController extends Controller
{
    public function getUsers()
    {
        $users = User::where('usertype', '!=', 'admin')->get();
        return view('admin/usersTable', compact('users'));
    }

    public function editUser(User $user)
    {
        // Ensure the user is not an admin
        if ($user->role == 'admin') {
            abort(403, 'Unauthorized action.');
        }

        return view('admin/editUser', compact('user'));
    }

    public function createUser()
    {
        return view('admin/createUser');
    }

    public function storeUser(Request $request)
    {
        $validatedData = $request->validate([
            'usertype' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|max:12',
        ]);

        try {
            $validatedData['password'] = bcrypt($validatedData['password']);
            $user = User::create($validatedData);
            return redirect()->route('admin.users')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'User creation failed. Please try again.']);
        }
    }

    public function updateUser(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'usertype' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'address' => 'required|string|max:255',

        ]);

        try {
            $user->update($validatedData);
            return redirect()->route('admin.users')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'User update failed. Please try again.']);
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
}
