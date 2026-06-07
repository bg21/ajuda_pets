<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Auth;

// Rota de Login (Aberta)
Volt::route('login', 'portal.login')->name('portal.login');

// Rotas Protegidas (Exigem Login do Tutor)
Route::middleware(['auth:tutor'])->prefix('portal')->name('portal.')->group(function () {
    Volt::route('/', 'portal.dashboard')->name('dashboard');
    Volt::route('/pet/{pet_id}', 'portal.pet')->name('pet');
    Volt::route('/agendamentos', 'portal.agendamentos')->name('agendamentos');
    Volt::route('/financeiro', 'portal.financeiro')->name('financeiro');

    // Logout
    Route::post('/logout', function () {
        Auth::guard('tutor')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('portal.login');
    })->name('logout');
});
