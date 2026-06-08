<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Rotas de Autenticação (Abertas)
Volt::route('login', 'portal.login')->name('portal.login');
Volt::route('registro', 'portal.register')->name('portal.register');

// Rota de Identidade Digital (QR Code) - Aberta
Route::get('/p/{uuid}', function ($uuid) {
    $pet = \App\Models\Pet::where('uuid', $uuid)->firstOrFail();
    return view('public.pet', compact('pet'));
})->name('pet.public');

// Rotas Protegidas (Exigem Login do Tutor)
Route::middleware(['auth'])->prefix('portal')->name('portal.')->group(function () {
    Volt::route('/', 'portal.dashboard')->name('dashboard');
    Volt::route('/pet/{pet_id}', 'portal.pet')->name('pet');
    Volt::route('/lembretes', 'portal.agendamentos')->name('agendamentos');
    Volt::route('/financeiro', 'portal.financeiro')->name('financeiro');
    Volt::route('/checkout', 'portal.checkout')->name('checkout');
    Volt::route('/perfil', 'portal.perfil')->name('perfil');

    // Logout
    Route::post('/logout', function () {
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('portal.login');
    })->name('logout');
});
