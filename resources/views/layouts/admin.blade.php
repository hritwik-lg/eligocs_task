<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link.active { @apply bg-indigo-700 text-white; }
    </style>
</head>
<body class="h-full">
<div class="min-h-full flex">

    {{-- Sidebar --}}
    <div class="hidden md:flex md:w-64 md:flex-col">
        <div class="flex flex-col flex-grow pt-5 bg-indigo-900 overflow-y-auto">
            {{-- Logo --}}
            <div class="flex items-center flex-shrink-0 px-4 pb-4 border-b border-indigo-700">
                <div class="w-9 h-9 bg-indigo-400 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-semibold text-sm">{{ config('app.name') }}</p>
                    <p class="text-indigo-300 text-xs">Tenant Admin Panel</p>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="mt-5 flex-1 px-3 space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-indigo-100 hover:bg-indigo-700 transition">
                    <svg class="mr-3 h-5 w-5 text-indigo-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.tenants.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.tenants*') ? 'active' : '' }} group flex items-center px-3 py-2 text-sm font-medium rounded-lg text-indigo-100 hover:bg-indigo-700 transition">
                    <svg class="mr-3 h-5 w-5 text-indigo-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Tenants
                </a>
            </nav>

            {{-- Admin info --}}
            <div class="flex-shrink-0 flex border-t border-indigo-700 p-4">
                <div class="flex items-center w-full">
                    <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center">
                        <span class="text-xs font-medium text-white">{{ substr(auth('super_admin')->user()->name ?? 'SA', 0, 2) }}</span>
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth('super_admin')->user()->name ?? 'Tenant Admin' }}</p>
                        <p class="text-xs text-indigo-300 truncate">{{ auth('super_admin')->user()->email ?? '' }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="text-indigo-300 hover:text-white p-1 rounded" title="Logout">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <div class="flex flex-col flex-1 overflow-hidden">
        {{-- Top bar --}}
        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
            <div class="flex items-center space-x-3">
                @yield('header-actions')
            </div>
        </div>

        {{-- Flash messages --}}
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">✕</button>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('error') }}
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">✕</button>
                </div>
            @endif
        </div>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto px-6 py-4">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>