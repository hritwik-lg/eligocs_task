@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center space-x-4">
            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_tenants'] }}</p>
                <p class="text-sm text-gray-500">Total Tenants</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center space-x-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['active_tenants'] }}</p>
                <p class="text-sm text-gray-500">Active Tenants</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center space-x-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['inactive_tenants'] }}</p>
                <p class="text-sm text-gray-500">Inactive Tenants</p>
            </div>
        </div>
    </div>

    {{-- Recent Tenants --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800">Recent Tenants</h2>
            <a href="{{ route('admin.tenants.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View all →</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($stats['recent_tenants'] as $tenant)
            <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-indigo-700 font-semibold text-sm">{{ substr($tenant->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $tenant->name }}</p>
                        <p class="text-xs text-gray-500">{{ $tenant->email }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $tenant->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($tenant->status) }}
                    </span>
                    <a href="{{ route('admin.tenants.show', $tenant) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">View</a>
                </div>
            </div>
            @empty
            <div class="px-6 py-8 text-center text-gray-400 text-sm">
                No tenants yet. <a href="{{ route('admin.tenants.create') }}" class="text-indigo-600 hover:underline">Create the first one</a>.
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection