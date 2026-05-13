@extends('layouts.tenant')

@section('title', 'Tasks')
@section('page-title', 'Tasks')

@section('header-actions')
    <a href="{{ route('tenant.tasks.create') }}"
       class="inline-flex items-center px-4 py-2 bg-sky-600 text-white text-sm font-medium rounded-lg hover:bg-sky-700 transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Task
    </a>
@endsection

@section('content')
<div class="space-y-5">

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 flex-wrap">
            <div class="flex-1 min-w-48">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500"
                       placeholder="Search tasks…">
            </div>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500">
                <option value="">All Status</option>
                @foreach($statuses as $val => $label)
                    <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="priority" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500">
                <option value="">All Priority</option>
                @foreach($priorities as $val => $label)
                    <option value="{{ $val }}" @selected(request('priority') === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-sky-600 text-white text-sm rounded-lg hover:bg-sky-700 transition">Filter</button>
            @if(request('search') || request('status') || request('priority'))
                <a href="{{ route('tenant.tasks.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition">Clear</a>
            @endif
        </form>
    </div>

    {{-- Task list --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($tasks as $task)
                <tr class="hover:bg-gray-50 transition {{ $task->isOverdue() ? 'bg-red-50' : '' }}">
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-gray-900">{{ $task->title }}</p>
                        @if($task->description)
                            <p class="text-xs text-gray-500 mt-0.5 truncate max-w-xs">{{ $task->description }}</p>
                        @endif
                        @if($task->user)
                            <p class="text-xs text-gray-400 mt-0.5">{{ $task->user->name }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($task->status === 'done') bg-green-100 text-green-700
                            @elseif($task->status === 'in_progress') bg-blue-100 text-blue-700
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ str_replace('_', ' ', ucfirst($task->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($task->priority === 'high') bg-red-100 text-red-700
                            @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-700
                            @else bg-green-100 text-green-700 @endif">
                            {{ ucfirst($task->priority) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($task->due_date)
                            <span class="{{ $task->isOverdue() ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                                {{ $task->due_date->format('M d, Y') }}
                                @if($task->isOverdue()) <span class="block text-xs">Overdue</span>@endif
                            </span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('tenant.tasks.edit', $task) }}"
                               class="text-sky-600 hover:text-sky-800 text-sm font-medium">Edit</a>
                            <form method="POST" action="{{ route('tenant.tasks.destroy', $task) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium"
                                        onclick="return confirm('Delete this task?')">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                        No tasks found.
                        <a href="{{ route('tenant.tasks.create') }}" class="text-sky-600 hover:underline">Create one?</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($tasks->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $tasks->links() }}</div>
        @endif
    </div>
</div>
@endsection
