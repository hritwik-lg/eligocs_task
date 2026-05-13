<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total_tenants'    => Tenant::count(),
            'active_tenants'   => Tenant::active()->count(),
            'inactive_tenants' => Tenant::inactive()->count(),
            'recent_tenants'   => Tenant::latest()->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
