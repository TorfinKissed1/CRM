@php use App\Support\Crm; @endphp
<div class="login">
    <div class="login__card">
        <div class="login__head">
            <span class="login__logo">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="login-logo-grad" x1="0" y1="0" x2="24" y2="24" gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#c084fc"/>
                            <stop offset="100%" stop-color="#6a40c9"/>
                        </linearGradient>
                    </defs>
                    <path d="M18.5 12.5A7.5 7.5 0 0 1 8 4.18 8 8 0 1 0 19.82 16 7.5 7.5 0 0 1 18.5 12.5Z" fill="url(#login-logo-grad)"/>
                    <polygon points="19,3 20,6 23,6 20.5,8 21.5,11 19,9 16.5,11 17.5,8 15,6 18,6" fill="#f0abfc"/>
                </svg>
            </span>
            <h1 class="login__title">{{ Crm::businessName() }}</h1>
            <p class="login__sub">Вход в систему</p>
        </div>

        <form class="form" wire:submit="login">
            <label class="field">
                <span class="field__label">Email</span>
                <input type="email" class="field__input" wire:model="email" autocomplete="username" autofocus>
                @error('email') <span class="field__error">{{ $message }}</span> @enderror
            </label>

            <label class="field">
                <span class="field__label">Пароль</span>
                <input type="password" class="field__input" wire:model="password" autocomplete="current-password">
                @error('password') <span class="field__error">{{ $message }}</span> @enderror
            </label>

            <label class="checkbox">
                <input type="checkbox" class="checkbox__input" wire:model="remember">
                <span class="checkbox__label">Запомнить меня</span>
            </label>

            <button type="submit" class="btn btn--primary btn--block">
                <span wire:loading.remove wire:target="login">Войти</span>
                <span wire:loading wire:target="login">Вход…</span>
            </button>
        </form>
    </div>
</div>
