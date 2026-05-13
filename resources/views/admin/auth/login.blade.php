@extends('layouts.auth')

@section('title', 'Super Admin Login')

@section('auth-logo')
    <div class="w-16 h-16 bg-indigo-500 rounded-2xl flex items-center justify-center mx-auto">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
    </div>
@endsection

@section('auth-heading', 'Super Admin Login')
@section('auth-subheading', 'Sign in to manage your tenants')

@section('auth-content')
    <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-400 @enderror"
                   placeholder="admin@example.com">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" id="password" name="password" required
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                   placeholder="••••••••">
        </div>

        <input type="hidden" name="remember" value="0">

        <div class="flex items-center justify-between">
            <label class="flex items-center">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" value="1" />
                <span class="ml-2 text-sm text-gray-600">Remember me</span>
            </label>
        </div>

        <button type="submit"
                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
            Sign in to Admin Panel
        </button>
    </form>
@endsection