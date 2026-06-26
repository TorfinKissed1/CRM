@php
    use App\Support\Crm;
    use Illuminate\Support\Carbon;

    $user = auth()->user();
    $nav = array_values(array_filter([
        ['route' => 'dashboard', 'label' => Crm::label('dashboard'), 'icon' => 'grid', 'show' => true],
        ['route' => 'clients', 'label' => Crm::label('clients'), 'icon' => 'users', 'show' => true],
        ['route' => 'schedule', 'label' => Crm::label('schedule'), 'icon' => 'calendar', 'show' => Crm::moduleEnabled('schedule')],
        ['route' => 'finance', 'label' => Crm::label('finance'), 'icon' => 'wallet', 'show' => Crm::moduleEnabled('finance')],
        ['route' => 'settings', 'label' => Crm::label('settings'), 'icon' => 'cog', 'show' => $user?->isOwner()],
    ], fn ($i) => $i['show']));

    $current = request()->route()?->getName();
    $pageTitle = collect($nav)->firstWhere('route', $current)['label'] ?? Crm::businessName();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }} · {{ Crm::businessName() }}</title>
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
<body class="app">
    <div class="app-shell">
        <aside class="sidebar">
            <a href="{{ route('dashboard') }}" wire:navigate class="sidebar__brand">
                <span class="sidebar__logo">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="logo-grad" x1="0" y1="0" x2="24" y2="24" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#c084fc"/>
                                <stop offset="100%" stop-color="#6a40c9"/>
                            </linearGradient>
                        </defs>
                        {{-- Полумесяц --}}
                        <path d="M18.5 12.5A7.5 7.5 0 0 1 8 4.18 8 8 0 1 0 19.82 16 7.5 7.5 0 0 1 18.5 12.5Z" fill="url(#logo-grad)" opacity="0.95"/>
                        {{-- Звезда --}}
                        <polygon points="19,3 20,6 23,6 20.5,8 21.5,11 19,9 16.5,11 17.5,8 15,6 18,6" fill="#f0abfc" opacity="0.9"/>
                    </svg>
                </span>
                <span class="sidebar__name">{{ Crm::businessName() }}</span>
            </a>

            <nav class="nav">
                @foreach ($nav as $item)
                    <a href="{{ route($item['route']) }}" wire:navigate
                       class="nav__item {{ $current === $item['route'] ? 'nav__item--active' : '' }}">
                        <span class="nav__icon">@include('partials.icons', ['name' => $item['icon']])</span>
                        <span class="nav__label">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="sidebar__foot">
                <div class="user">
                    <span class="avatar">{{ $user->initials() }}</span>
                    <div class="user__meta">
                        <span class="user__name">{{ $user->name }}</span>
                        <span class="user__role">{{ $user->role->label() }}</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn--ghost btn--sm btn--block">Выйти</button>
                </form>
            </div>
        </aside>

        <div class="app-main">
            <header class="topbar">
                <h1 class="topbar__title">{{ $pageTitle }}</h1>
                <div class="topbar__right">
                    <span class="topbar__date">{{ Carbon::now()->isoFormat('dddd, D MMMM') }}</span>
                    <button type="button" class="theme-toggle" aria-label="Сменить тему" title="Сменить тему"
                            onclick="(function(){var r=document.documentElement;var isLight=r.getAttribute('data-theme')==='light';if(isLight){r.removeAttribute('data-theme');try{localStorage.setItem('theme','dark');}catch(e){}}else{r.setAttribute('data-theme','light');try{localStorage.setItem('theme','light');}catch(e){}}})()">
                        <span class="theme-toggle__sun">@include('partials.icons', ['name' => 'sun'])</span>
                        <span class="theme-toggle__moon">@include('partials.icons', ['name' => 'moon'])</span>
                    </button>
                </div>
            </header>

            <main class="content">
                {{ $slot }}
            </main>
        </div>
    </div>
    @livewireScripts
</body>
</html>
