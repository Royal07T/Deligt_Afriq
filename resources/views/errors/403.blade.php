<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access denied | Delight Afriq</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grid min-h-screen place-items-center bg-cream px-6">
    <div class="da-card max-w-lg p-8 text-center">
        <p class="da-eyebrow">403</p>
        <h1 class="da-heading mt-3">Access denied</h1>
        <p class="mt-3 text-body">You do not have permission to view this page.</p>
        <a href="{{ route('home') }}" class="da-btn-primary mt-6">Return to storefront</a>
    </div>
</body>
</html>
