<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreTaskRequest;
use App\Http\Requests\Tenant\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    private array $statuses = [
        Task::STATUS_TODO        => 'To Do',
        Task::STATUS_IN_PROGRESS => 'In Progress',
        Task::STATUS_DONE        => 'Done',
    ];

    private array $priorities = [
        Task::PRIORITY_LOW    => 'Low',
        Task::PRIORITY_MEDIUM => 'Medium',
        Task::PRIORITY_HIGH   => 'High',
    ];

    /** List all tasks for this tenant workspace. */
    public function index(Request $request): View
    {
        $query = Task::query();

        if ($status = $request->get('status')) {
            $query->byStatus($status);
        }
        if ($priority = $request->get('priority')) {
            $query->byPriority($priority);
        }
        if ($search = $request->get('search')) {
            $query->where(fn ($q) =>
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%")
            );
        }

        $sortBy  = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc') === 'asc' ? 'asc' : 'desc';
        if (in_array($sortBy, ['title', 'status', 'priority', 'due_date', 'created_at'])) {
            $query->orderBy($sortBy, $sortDir);
        }

        $tasks = $query->paginate(15)->withQueryString();

        return view('tenant.tasks.index', [
            'tasks'      => $tasks,
            'statuses'   => $this->statuses,
            'priorities' => $this->priorities,
        ]);
    }

    /** Show create task form. */
    public function create(): View
    {
        return view('tenant.tasks.create', [
            'statuses'   => $this->statuses,
            'priorities' => $this->priorities,
        ]);
    }

    /** Store a new task — no user ownership needed. */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        Task::create([
            'user_id' => auth()->id(),
            'title'       => $request->title,
            'description' => $request->description,
            'status'      => $request->status   ?? Task::STATUS_TODO,
            'priority'    => $request->priority  ?? Task::PRIORITY_MEDIUM,
            'due_date'    => $request->due_date,
        ]);

        return redirect()->route('tenant.tasks.index')
                         ->with('success', 'Task created successfully.');
    }

    /** Show a specific task. */
    public function show(Task $task): View
    {
        return view('tenant.tasks.show', compact('task'));
    }

    /** Show edit form. */
    public function edit($id): View
    {

        $task = Task::findOrFail($id);

        return view('tenant.tasks.edit', [
            'task' => $task,
            'statuses' => $this->statuses,
            'priorities' => $this->priorities,
        ]);
    }

    /** Update task. */
    public function update(UpdateTaskRequest $request, $id): RedirectResponse
    {
        $task = Task::findOrFail($id);

        $task->update($request->validated());

        return redirect()->route('tenant.tasks.index')
                         ->with('success', 'Task updated successfully.');
    }

    /** Delete task. */
    public function destroy($id): RedirectResponse
    {
        $task = Task::findOrFail($id);

        $task->delete();

        return redirect()->route('tenant.tasks.index')
                         ->with('success', 'Task deleted successfully.');
    }

    /** Quick inline status update. */
    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $task = Task::findOrFail($id);

        $request->validate(['status' => 'required|in:todo,in_progress,done']);
        $task->update(['status' => $request->status]);

        return back()->with('success', 'Status updated.');
    }
}
