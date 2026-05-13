@extends('layouts.tenant')

@section('title', $task->title)
@section('page-title', 'Task Detail')

@section('header-actions')
    <a href="{{ route('tenant.tasks.edit', $task) }}"
       class="inline-flex items-center px-4 py-2 bg-sky-600 text-white text-sm font-medium rounded-lg hover:bg-sky-700 transition">
        Edit Task
    </a>
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $task->title }}</h2>
            @if($task->description)
                <p class="mt-2 text-gray-600 text-sm leading-relaxed">{{ $task->description }}</p>
            @endif
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-2">
            <div>
                <p class="text-xs text-gray-500 mb-1">Status</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($task->status === 'done') bg-green-100 text-green-700
                    @elseif($task->status === 'in_progress') bg-blue-100 text-blue-700
                    @else bg-gray-100 text-gray-600 @endif">
                    {{ str_replace('_', ' ', ucfirst($task->status)) }}
                </span>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Priority</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($task->priority === 'high') bg-red-100 text-red-700
                    @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-700
                    @else bg-green-100 text-green-700 @endif">
                    {{ ucfirst($task->priority) }}
                </span>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Due Date</p>
                <p class="text-sm {{ $task->isOverdue() ? 'text-red-600 font-medium' : 'text-gray-700' }}">
                    {{ $task->due_date ? $task->due_date->format('M d, Y') : '—' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Created</p>
                <p class="text-sm text-gray-700">{{ $task->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        <div class="pt-2 flex space-x-3">
            <a href="{{ route('tenant.tasks.index') }}"
               class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition">
                ← Back to Tasks
            </a>
            <form method="POST" action="{{ route('tenant.tasks.destroy', $task) }}">
                @csrf @method('DELETE')
                <button type="submit" onclick="return confirm('Delete this task?')"
                        class="px-4 py-2 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg hover:bg-red-100 transition">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
