<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the tenant workspace dashboard.
     * Fully public — no auth required.
     */
    public function index(): View
    {
        $stats = [
            'total'       => Task::count(),
            'todo'        => Task::byStatus(Task::STATUS_TODO)->count(),
            'in_progress' => Task::byStatus(Task::STATUS_IN_PROGRESS)->count(),
            'done'        => Task::byStatus(Task::STATUS_DONE)->count(),
            'overdue'     => Task::overdue()->count(),
            'high_priority' => Task::byPriority(Task::PRIORITY_HIGH)
                                   ->where('status', '!=', Task::STATUS_DONE)
                                   ->count(),
        ];

        $recentTasks = Task::latest()->take(6)->get();

        $overdueTasks = Task::overdue()
                            ->orderBy('due_date')
                            ->take(5)
                            ->get();

        return view('tenant.dashboard.index', compact('stats', 'recentTasks', 'overdueTasks'));
    }
}
