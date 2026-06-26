@php use App\Support\Crm; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? Crm::businessName() }}</title>
    @livewireStyles
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="auth">
    <main class="auth__frame">
        {{ $slot }}
    </main>
    @livewireScripts
</body>
</html>
