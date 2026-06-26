<?php

use App\Livewire\Auth\Login;
use App\Livewire\Clients\Import as ClientsImport;
use App\Livewire\Clients\Index as Clients;
use App\Livewire\Dashboard\Index as Dashboard;
use App\Livewire\Finance\Index as Finance;
use App\Livewire\Schedule\Index as Schedule;
use App\Livewire\Settings\Index as Settings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::redirect('/', '/dashboard');

    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/clients', Clients::class)->name('clients');
    Route::get('/clients/import', ClientsImport::class)->name('clients.import');
    Route::get('/schedule', Schedule::class)->name('schedule')->middleware('module:schedule');
    Route::get('/finance', Finance::class)->name('finance')->middleware('module:finance');
    Route::get('/settings', Settings::class)->name('settings')->middleware('can:owner');
});
