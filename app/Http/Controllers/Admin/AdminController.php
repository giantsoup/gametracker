<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function dashboard(): View
    {
        return view('admin.dashboard');
    }

    /**
     * Show the system settings page.
     */
    public function settings(): View
    {
        return view('admin.settings');
    }

    /**
     * Show the analytics page.
     */
    public function analytics(): View
    {
        return view('admin.analytics');
    }

    /**
     * Show the notifications page.
     */
    public function notifications(): View
    {
        return view('admin.notifications');
    }

    /**
     * Show the system logs page.
     */
    public function logs(): View
    {
        return view('admin.logs');
    }
}
