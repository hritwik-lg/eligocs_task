<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tenant Inactive</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg p-10 text-center">
        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-900 mb-2">Account Inactive</h1>
        <p class="text-gray-500 text-sm">
            @if($tenant)
                <strong>{{ $tenant->name }}</strong> is currently inactive.
            @else
                This workspace is currently inactive.
            @endif
            Please contact your administrator.
        </p>
    </div>
</body>
</html>
