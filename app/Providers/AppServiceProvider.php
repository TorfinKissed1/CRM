<?php

namespace App\Providers;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Русская локаль для Laravel и Carbon (дни недели/месяцы в дашборде и расписании).
        app()->setLocale('ru');
        Carbon::setLocale('ru');
        CarbonImmutable::setLocale('ru');

        // Роль владельца — доступ к Настройкам и управлению.
        Gate::define('owner', fn (User $user) => $user->isOwner());
    }
}
