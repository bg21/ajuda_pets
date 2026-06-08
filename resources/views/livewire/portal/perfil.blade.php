<?php

use function Livewire\Volt\{state, mount, rules};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

state([
    'name' => '',
    'email' => '',
    'current_password' => '',
    'password' => '',
    'password_confirmation' => '',
    'successMessage' => '',
]);

\Livewire\Volt\layout('components.layouts.app');

mount(function () {
    $this->name = Auth::user()->name;
    $this->email = Auth::user()->email;
});

$saveProfile = function () {
    $user = Auth::user();

    $this->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
    ]);

    $user->update([
        'name' => $this->name,
        'email' => $this->email,
    ]);

    $this->successMessage = 'Perfil atualizado com sucesso!';
};

$updatePassword = function () {
    $user = Auth::user();

    $this->validate([
        'current_password' => 'required|current_password',
        'password' => 'required|string|min:8|confirmed',
    ], [
        'current_password.current_password' => 'A senha atual está incorreta.',
    ]);

    $user->update([
        'password' => Hash::make($this->password),
    ]);

    $this->reset(['current_password', 'password', 'password_confirmation']);
    $this->successMessage = 'Senha atualizada com sucesso!';
};

?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-slot name="title">Meu Perfil</x-slot>

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-slate-800">Meu Perfil</h1>
            <p class="text-slate-500 mt-1 font-medium">Gerencie suas informações pessoais e credenciais de acesso.</p>
        </div>
    </div>

    @if($successMessage)
        <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 rounded-xl font-bold flex items-center gap-3 border border-emerald-100" x-data="{ show: true }" x-show="show">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $successMessage }}
            <button @click="show = false" class="ml-auto text-emerald-500 hover:text-emerald-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Coluna Esquerda: Informações Básicas -->
        <div class="md:col-span-2 space-y-8">
            
            <!-- Dados Pessoais -->
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-teal-50 rounded-full blur-3xl -mr-10 -mt-10 pointer-events-none"></div>
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Dados Pessoais
                </h3>

                <form wire:submit="saveProfile" class="space-y-5 relative z-10">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nome Completo</label>
                        <input type="text" wire:model="name" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-medium focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all">
                        @error('name') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Endereço de E-mail</label>
                        <input type="email" wire:model="email" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-medium focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all">
                        @error('email') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-6 rounded-xl transition-colors shadow-md shadow-teal-500/20">
                            <span wire:loading.remove wire:target="saveProfile">Salvar Alterações</span>
                            <span wire:loading wire:target="saveProfile">Salvando...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Segurança -->
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 relative overflow-hidden">
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Segurança e Senha
                </h3>

                <form wire:submit="updatePassword" class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Senha Atual</label>
                        <input type="password" wire:model="current_password" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-medium focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all">
                        @error('current_password') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nova Senha</label>
                            <input type="password" wire:model="password" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-medium focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all">
                            @error('password') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Confirmar Nova Senha</label>
                            <input type="password" wire:model="password_confirmation" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-medium focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-bold py-3 px-6 rounded-xl transition-colors shadow-md">
                            <span wire:loading.remove wire:target="updatePassword">Atualizar Senha</span>
                            <span wire:loading wire:target="updatePassword">Atualizando...</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- Coluna Direita: Status da Conta -->
        <div class="space-y-6">
            <div class="bg-slate-900 p-8 rounded-[2rem] shadow-xl text-white">
                <h3 class="text-lg font-black mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Status da Assinatura
                </h3>
                
                @if(Auth::user()->subscription_status === 'ACTIVE')
                    <div class="bg-teal-500/20 border border-teal-400/30 p-4 rounded-xl text-center mb-6">
                        <p class="text-teal-300 font-bold uppercase tracking-wider text-xs mb-1">Plano Atual</p>
                        <p class="text-2xl font-black text-white capitalize">{{ Auth::user()->plan_type }}</p>
                    </div>
                    <ul class="space-y-3 text-sm font-medium text-slate-300 mb-6">
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg> Pets ilimitados (Max) ou 10 (Pro)</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg> Agendamentos completos</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg> Suporte prioritário</li>
                    </ul>
                @elseif(Auth::user()->subscription_status === 'PENDING')
                    <div class="bg-orange-500/20 border border-orange-400/30 p-4 rounded-xl text-center mb-6">
                        <p class="text-orange-300 font-bold uppercase tracking-wider text-xs mb-1">Pagamento</p>
                        <p class="text-xl font-black text-white">Pendente</p>
                    </div>
                    <p class="text-slate-300 text-sm mb-6 text-center font-medium">Você iniciou a assinatura, mas o pagamento ainda não foi confirmado.</p>
                    <a href="{{ route('portal.checkout', ['plan' => Auth::user()->plan_type]) }}" class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-center font-bold py-3 px-4 rounded-xl transition-colors">
                        Finalizar Pagamento
                    </a>
                @else
                    <div class="bg-slate-700/50 border border-slate-600 p-4 rounded-xl text-center mb-6">
                        <p class="text-slate-400 font-bold uppercase tracking-wider text-xs mb-1">Plano Atual</p>
                        <p class="text-xl font-black text-white">Grátis</p>
                    </div>
                    <p class="text-slate-300 text-sm mb-6 text-center font-medium">Faça upgrade para adicionar mais pets e gerenciar a saúde deles com IA.</p>
                    <a href="{{ route('home') }}#planos" class="block w-full bg-white text-slate-900 text-center font-black py-3 px-4 rounded-xl transition-colors hover:bg-slate-100">
                        Fazer Upgrade Agora
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
