@extends('layouts.tenant')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('header-actions')
    <a href="{{ route('tenant.tasks.create') }}"
       class="inline-flex items-center px-4 py-2 bg-sky-600 text-white text-sm font-medium rounded-lg hover:bg-sky-700 transition">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Task
    </a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        @php
        $cards = [
            ['label'=>'Total',        'value'=>$stats['total'],        'bg'=>'bg-slate-100',  'text'=>'text-slate-700',  'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['label'=>'To Do',        'value'=>$stats['todo'],         'bg'=>'bg-blue-50',    'text'=>'text-blue-700',   'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label'=>'In Progress',  'value'=>$stats['in_progress'],  'bg'=>'bg-yellow-50',  'text'=>'text-yellow-700', 'icon'=>'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
            ['label'=>'Done',         'value'=>$stats['done'],         'bg'=>'bg-green-50',   'text'=>'text-green-700',  'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label'=>'Overdue',      'value'=>$stats['overdue'],      'bg'=>'bg-red-50',     'text'=>'text-red-700',    'icon'=>'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
            ['label'=>'High Priority','value'=>$stats['high_priority'],'bg'=>'bg-orange-50',  'text'=>'text-orange-700', 'icon'=>'M13 10V3L4 14h7v7l9-11h-7z'],
        ];
        @endphp

        @foreach($cards as $card)
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between mb-3">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg {{ $card['bg'] }}">
                    <svg class="w-4 h-4 {{ $card['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $card['value'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ $card['label'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Recent tasks --}}
        <div class="xl:col-span-2 bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800 text-sm">Recent Tasks</h2>
                <a href="{{ route('tenant.tasks.index') }}" class="text-xs text-sky-600 hover:text-sky-800 font-medium">View all →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentTasks as $task)
                <div class="px-5 py-3.5 flex items-center justify-between hover:bg-gray-50 transition group">
                    <div class="flex items-center space-x-3 min-w-0">
                        {{-- Inline status toggle form --}}
                        <form method="POST" action="{{ route('tenant.tasks.status', $task) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $task->status === 'done' ? 'todo' : 'done' }}">
                            <button type="submit"
                                    class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition
                                           {{ $task->status === 'done' ? 'bg-green-500 border-green-500 text-white' : 'border-gray-300 hover:border-green-400' }}"
                                    title="{{ $task->status === 'done' ? 'Mark incomplete' : 'Mark complete' }}">
                                @if($task->status === 'done')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @endif
                            </button>
                        </form>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate {{ $task->status === 'done' ? 'line-through text-gray-400' : '' }}">
                                {{ $task->title }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $task->due_date ? 'Due '.$task->due_date->format('M d') : 'No due date' }}
                                @if($task->isOverdue()) · <span class="text-red-500">Overdue</span> @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 flex-shrink-0">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            @if($task->priority==='high') bg-red-100 text-red-700
                            @elseif($task->priority==='medium') bg-yellow-100 text-yellow-700
                            @else bg-green-100 text-green-700 @endif">
                            {{ ucfirst($task->priority) }}
                        </span>
                        <a href="{{ route('tenant.tasks.edit', $task) }}"
                           class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-sky-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                @empty
                <div class="px-5 py-10 text-center">
                    <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-sm text-gray-400">No tasks yet.</p>
                    <a href="{{ route('tenant.tasks.create') }}" class="mt-2 inline-block text-sm text-sky-600 hover:underline font-medium">
                        Create your first task →
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Overdue sidebar --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800 text-sm">Overdue</h2>
                @if($stats['overdue'] > 0)
                    <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $stats['overdue'] }}</span>
                @endif
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($overdueTasks as $task)
                <div class="px-5 py-3.5">
                    <div class="flex items-start justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $task->title }}</p>
                            <p class="text-xs text-red-500 mt-0.5">
                                {{ $task->due_date->diffForHumans() }}
                            </p>
                        </div>
                        <a href="{{ route('tenant.tasks.edit', $task) }}"
                           class="ml-2 text-xs text-sky-600 hover:underline flex-shrink-0">Edit</a>
                    </div>
                </div>
                @empty
                <div class="px-5 py-8 text-center">
                    <svg class="w-8 h-8 text-green-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-gray-400">All caught up!</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
