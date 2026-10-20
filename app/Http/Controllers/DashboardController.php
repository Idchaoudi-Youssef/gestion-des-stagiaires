<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Sends every user to the page that matches their role.
 */
class DashboardController extends Controller
{
    /**
     * Redirect the user to the dashboard of their role.
     */
    public function index(Request $request): RedirectResponse
    {
        return redirect()->route($request->user()->homeRoute());
    }

    /**
     * Page shown to the accounts that are still waiting for an administrator
     * to give them a role.
     */
    public function pending(Request $request): View|RedirectResponse
    {
        if ($request->user()->usertype !== UserType::Pending) {
            return redirect()->route($request->user()->homeRoute());
        }

        return view('pending');
    }
}
