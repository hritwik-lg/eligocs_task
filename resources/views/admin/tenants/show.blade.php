@extends('layouts.admin')

@section('title', $tenant->name)
@section('page-title', $tenant->name)

@section('header-actions')
    <a href="{{ route('admin.tenants.edit', $tenant) }}"
       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
        Edit
    </a>
    @if($tenant->isActive())
        <form method="POST" action="{{ route('admin.tenants.deactivate', $tenant) }}" class="inline">
            @csrf @method('PATCH')
            <button type="submit" onclick="return confirm('Deactivate this tenant?')"
                    class="inline-flex items-center px-4 py-2 bg-yellow-50 border border-yellow-300 text-yellow-700 text-sm font-medium rounded-lg hover:bg-yellow-100 transition">
                Deactivate
            </button>
        </form>
    @else
        <form method="POST" action="{{ route('admin.tenants.activate', $tenant) }}" class="inline">
            @csrf @method('PATCH')
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-green-50 border border-green-300 text-green-700 text-sm font-medium rounded-lg hover:bg-green-100 transition">
                Activate
            </button>
        </form>
    @endif
@endsection

@section('content')
<div class="space-y-5 max-w-4xl">

    {{-- Tenant info card --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-start space-x-4">
            <div class="w-16 h-16 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <span class="text-indigo-700 font-bold text-2xl">{{ substr($tenant->name, 0, 1) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center space-x-3 flex-wrap gap-y-2">
                    <h2 class="text-xl font-bold text-gray-900">{{ $tenant->name }}</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($tenant->status === 'active') bg-green-100 text-green-700
                        @elseif($tenant->status === 'suspended') bg-red-100 text-red-700
                        @else bg-gray-100 text-gray-600 @endif">
                        {{ ucfirst($tenant->status) }}
                    </span>
                </div>
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-500">Email:</span> <span class="text-gray-800">{{ $tenant->email }}</span></div>
                    <div><span class="text-gray-500">Phone:</span> <span class="text-gray-800">{{ $tenant->phone ?? '—' }}</span></div>
                    <div><span class="text-gray-500">Slug:</span> <code class="font-mono bg-gray-100 px-1 rounded">{{ $tenant->slug }}</code></div>
                    <div><span class="text-gray-500">Schema:</span> <code class="font-mono bg-gray-100 px-1 rounded">{{ $tenant->schema_name }}</code></div>
                    <div><span class="text-gray-500">Domain:</span> <span class="text-gray-800">{{ $tenant->full_domain }}</span></div>
                    <div><span class="text-gray-500">Activated:</span> <span class="text-gray-800">{{ $tenant->activated_at?->format('M d, Y') ?? '—' }}</span></div>
                    <div><span class="text-gray-500">Created:</span> <span class="text-gray-800">{{ $tenant->created_at->format('M d, Y') }}</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    @if($stats['schema_exists'] ?? false)
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
            <p class="text-3xl font-bold text-gray-900">{{ $stats['users_count'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Users</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
            <p class="text-3xl font-bold text-gray-900">{{ $stats['tasks_count'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Tasks</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
            <p class="text-3xl font-bold text-green-600">{{ $stats['tasks_done'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Completed Tasks</p>
        </div>
    </div>
    @endif

    {{-- Danger zone --}}
    <div class="bg-white rounded-xl border border-red-200 p-6">
        <h3 class="text-sm font-semibold text-red-700 mb-3">Danger Zone</h3>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-700 font-medium">Delete this tenant</p>
                <p class="text-xs text-gray-500">This will soft-delete the tenant record. The schema remains intact.</p>
            </div>
            <form method="POST" action="{{ route('admin.tenants.destroy', $tenant) }}">
                @csrf @method('DELETE')
                <button type="submit" onclick="return confirm('Delete {{ $tenant->name }}?')"
                        class="px-4 py-2 bg-red-50 border border-red-300 text-red-700 text-sm rounded-lg hover:bg-red-100 transition">
                    Delete Tenant
                </button>
            </form>
        </div>
    </div>
</div>
@endsection