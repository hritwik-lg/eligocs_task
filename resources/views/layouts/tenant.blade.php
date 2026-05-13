<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Workspace') — {{ $currentTenant->name ?? 'SaaS App' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="h-full">
<div class="min-h-full flex">

    {{-- Sidebar --}}
    <div class="hidden md:flex md:w-64 md:flex-col flex-shrink-0">
        <div class="flex flex-col flex-grow pt-5 bg-slate-900 overflow-y-auto">

            {{-- Tenant brand --}}
            <div class="flex items-center px-5 pb-5 border-b border-slate-700">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 text-white font-bold text-lg"
                     style="background: linear-gradient(135deg, #0ea5e9, #6366f1);">
                    {{ substr($currentTenant->name ?? 'T', 0, 1) }}
                </div>
                <div class="ml-3 min-w-0">
                    <p class="text-white font-semibold text-sm truncate">{{ $currentTenant->name ?? 'Workspace' }}</p>
                    <p class="text-slate-400 text-xs">Task Manager</p>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="mt-4 flex-1 px-3 space-y-1">
                <a href="{{ route('tenant.dashboard') }}"
                   class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition
                          {{ request()->routeIs('tenant.dashboard') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('tenant.tasks.index') }}"
                   class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition
                          {{ request()->routeIs('tenant.tasks.index') || request()->routeIs('tenant.tasks.show') || request()->routeIs('tenant.tasks.edit') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    All Tasks
                </a>

                <a href="{{ route('tenant.tasks.create') }}"
                   class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition
                          {{ request()->routeIs('tenant.tasks.create') ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Task
                </a>
            </nav>

            {{-- Workspace URL footer --}}
            <div class="px-4 py-4 border-t border-slate-700">
                <p class="text-xs text-slate-500 truncate">
                    {{ $currentTenant->slug ?? '' }}.{{ config('tenancy.central_domain') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Main content area --}}
    <div class="flex flex-col flex-1 overflow-hidden">

        {{-- Top bar --}}
        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
            <div class="flex items-center space-x-3">
                @yield('header-actions')
            </div>
        </div>

        {{-- Flash messages --}}
        <div class="px-6 pt-4 space-y-3">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center text-sm">
                        <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700 ml-4 text-lg leading-none">×</button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 flex items-center justify-between text-sm">
                    <span>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="ml-4 text-lg leading-none">×</button>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto px-6 py-5">
            @yield('content')
        </main>

    </div>
</div>
</body>
</html>