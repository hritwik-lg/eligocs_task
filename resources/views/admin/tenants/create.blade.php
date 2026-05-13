@extends('layouts.admin')

@section('title', 'Create Tenant')
@section('page-title', 'Create Tenant')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.tenants.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Company Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-400 @enderror"
                           placeholder="Acme Corporation">
                    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Slug <span class="text-gray-400 text-xs">(auto-generated)</span>
                    </label>
                    <input type="text" name="slug" value="{{ old('slug') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono @error('slug') border-red-400 @enderror"
                           placeholder="acme-corporation">
                    @error('slug')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-gray-400">Used as subdomain: slug.{{ config('tenancy.central_domain') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Contact Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-400 @enderror"
                           placeholder="admin@company.com">
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="+1-555-0100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Custom Domain</label>
                    <input type="text" name="domain" value="{{ old('domain') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="app.company.com (optional)">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Initial Status</label>
                    <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="active" @selected(old('status') === 'active')>Active</option>
                        <option value="inactive" @selected(old('status', 'inactive') === 'inactive')>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-sm text-blue-700">
                <strong>Note:</strong> Creating a tenant will automatically provision a dedicated PostgreSQL schema (<code class="font-mono bg-blue-100 px-1 rounded">tenant_{slug}</code>) with all required tables.
            </div>

            <div class="flex items-center space-x-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    Create & Provision Tenant
                </button>
                <a href="{{ route('admin.tenants.index') }}"
                   class="px-6 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const nameInput = document.querySelector('[name="name"]');
    const slugInput = document.querySelector('[name="slug"]');

    if (!nameInput || !slugInput) return;

    // Auto-generate slug from FIRST word only
    nameInput.addEventListener('input', function () {

        if (!slugInput.dataset.modified) {

            const firstWord = this.value.split(' ')[0]; // 👈 only first name

            slugInput.value = firstWord
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '_')   // convert special chars to _
                .replace(/^_+|_+$/g, '');      // trim _ from ends
        }
    });

    // Prevent invalid characters in manual slug input
    slugInput.addEventListener('input', function () {
        this.value = this.value
            .toLowerCase()
            .replace(/[^a-z0-9_]/g, ''); // only allow safe chars

        this.dataset.modified = 'true';
    });

});
</script>
@endsection