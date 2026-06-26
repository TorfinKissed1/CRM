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
    @livewireStyles
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="app">
    <div class="app-shell">
        <aside class="sidebar">
            <a href="{{ route('dashboard') }}" wire:navigate class="sidebar__brand">
                <span class="sidebar__logo">{{ mb_strtoupper(mb_substr(Crm::businessName(), 0, 1)) }}</span>
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
                <div class="topbar__date">{{ Carbon::now()->isoFormat('dddd, D MMMM') }}</div>
            </header>

            <main class="content">
                {{ $slot }}
            </main>
        </div>
    </div>
    @livewireScripts
</body>
</html>
