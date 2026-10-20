<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * The profile of the logged in user.
     */
    public function index(): View
    {
        return view('profile');
    }

    /**
     * Update the profile of the logged in user.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'address' => ['nullable', 'string', 'max:255'],
            'current_password' => ['nullable', 'required_with:new_password'],
            'new_password' => ['nullable', 'required_with:current_password', 'min:8', 'max:12'],
            'password_confirmation' => ['nullable', 'required_with:new_password', 'same:new_password'],
        ]);

        // Changing the email address means the new address has to be
        // verified again.
        $emailVerifiedAt = $validated['email'] === $user->email
            ? $user->email_verified_at
            : null;

        $user->fill([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'email' => $validated['email'],
        ]);

        if (! empty($validated['current_password'])) {
            if (! Hash::check($validated['current_password'], $user->password)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['current_password' => 'The current password is incorrect.']);
            }

            $user->password = $validated['new_password'];
        }

        $user->email_verified_at = $emailVerifiedAt;
        $user->save();

        return redirect()
            ->route('profile')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete the account of the logged in user.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required'],
        ]);

        $user = $request->user();

        if (! Hash::check($request->input('password'), $user->password)) {
            return back()
                ->withInput()
                ->withErrors(['password' => 'The password does not match our records.']);
        }

        Auth::logout();
        $user->delete();

        return redirect('/')
            ->with('success', 'Your account has been deleted.');
    }
}
