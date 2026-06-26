@php use App\Support\Crm; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? Crm::businessName() }}</title>
    <script>
(function () {
    function applyTheme() {
        try {
            var t = localStorage.getItem('theme');
            if (t === 'light') document.documentElement.setAttribute('data-theme', 'light');
            else document.documentElement.removeAttribute('data-theme');
        } catch (e) {}
    }
    applyTheme();
    document.addEventListener('livewire:navigated', applyTheme);
})();
</script>
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
