@extends('layouts.admin')

@section('title', 'Tenants')
@section('page-title', 'Tenants')

@section('header-actions')
    <a href="{{ route('admin.tenants.create') }}"
       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Tenant
    </a>
@endsection

@section('content')
<div class="space-y-5">

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="Search by name, email or slug…">
            </div>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">All Status</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition">
                Filter
            </button>
            @if(request('search') || request('status'))
                <a href="{{ route('admin.tenants.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug / Schema</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($tenants as $tenant)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-indigo-700 font-semibold text-sm">{{ substr($tenant->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $tenant->name }}</p>
                                <p class="text-xs text-gray-500">{{ $tenant->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-800 font-mono">{{ $tenant->slug }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ $tenant->schema_name }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($tenant->status === 'active') bg-green-100 text-green-700
                            @elseif($tenant->status === 'suspended') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ ucfirst($tenant->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $tenant->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.tenants.show', $tenant) }}"
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View</a>
                            @if($tenant->isActive())
                                <form method="POST" action="{{ route('admin.tenants.deactivate', $tenant) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium"
                                            onclick="return confirm('Deactivate {{ $tenant->name }}?')">
                                        Deactivate
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.tenants.activate', $tenant) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-green-600 hover:text-green-800 text-sm font-medium">
                                        Activate
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.tenants.destroy', $tenant) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium"
                                        onclick="return confirm('Delete {{ $tenant->name }}? This cannot be undone.')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                        No tenants found.
                        <a href="{{ route('admin.tenants.create') }}" class="text-indigo-600 hover:underline">Create one?</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($tenants->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $tenants->links() }}
        </div>
        @endif
    </div>
</div>
@endsection