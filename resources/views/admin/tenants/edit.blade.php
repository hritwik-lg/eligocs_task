@extends('layouts.admin')

@section('title', 'Edit ' . $tenant->name)
@section('page-title', 'Edit Tenant')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.tenants.update', $tenant) }}" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Company Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 @error('name') border-red-400 @enderror">
                    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                    <input type="text" value="{{ $tenant->slug }}" disabled
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 font-mono cursor-not-allowed">
                    <p class="mt-1 text-xs text-gray-400">Slug cannot be changed after creation.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $tenant->email) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 @error('email') border-red-400 @enderror">
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Custom Domain</label>
                    <input type="text" name="domain" value="{{ old('domain', $tenant->domain) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="active" @selected(old('status', $tenant->status) === 'active')>Active</option>
                        <option value="inactive" @selected(old('status', $tenant->status) === 'inactive')>Inactive</option>
                        <option value="suspended" @selected(old('status', $tenant->status) === 'suspended')>Suspended</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center space-x-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    Save Changes
                </button>
                <a href="{{ route('admin.tenants.show', $tenant) }}"
                   class="px-6 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection