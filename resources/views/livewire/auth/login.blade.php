@php use App\Support\Crm; @endphp
<div class="login">
    <div class="login__card">
        <div class="login__head">
            <span class="login__logo">{{ mb_strtoupper(mb_substr(Crm::businessName(), 0, 1)) }}</span>
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
