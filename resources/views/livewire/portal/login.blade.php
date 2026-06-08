<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

new #[Layout('components.layouts.guest')] class extends Component {
    public $email = '';
    public $password = '';
    public $errorMessage = '';
    public $plan = null;

    public function mount()
    {
        $this->plan = request()->query('plan');
    }

    public function authenticate()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            request()->session()->regenerate();
            
            if (in_array($this->plan, ['pro', 'max'])) {
                return redirect()->route('portal.checkout', ['plan' => $this->plan]);
            }
            
            return redirect()->route('portal.dashboard');
        }

        $this->errorMessage = 'E-mail ou senha incorretos.';
    }
}; ?>

<div class="w-full max-w-md bg-white p-8 rounded-3xl shadow-xl border border-slate-100">
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-primary rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-700/20 text-white">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">AjudaPet</h1>
        <p class="text-slate-500 mt-1">
            @if($plan === 'pro') Acesse para Assinar o Plano Pro @elseif($plan === 'max') Acesse para Assinar o Plano Max @else Acompanhe a saúde do seu pet de perto @endif
        </p>
    </div>

    <form wire:submit="authenticate" class="space-y-5">
        @if($errorMessage)
            <div class="bg-red-50 text-red-600 p-3 rounded-xl text-sm font-semibold text-center border border-red-100">
                {{ $errorMessage }}
            </div>
        @endif

        <div>
            <label class="block text-sm font-semibold text-slate-600 mb-2">E-mail</label>
            <input type="email" wire:model="email" placeholder="tutor@email.com" class="w-full border border-slate-200 bg-slate-50 rounded-xl px-4 py-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" required>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-600 mb-2">Senha</label>
            <input type="password" wire:model="password" placeholder="••••••••" class="w-full border border-slate-200 bg-slate-50 rounded-xl px-4 py-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" required>
        </div>

        <button type="submit" class="w-full bg-accent hover:bg-accent-dark text-white font-bold py-3.5 rounded-xl shadow-lg shadow-orange-500/30 transition transform hover:-translate-y-0.5 mt-2">
            <span wire:loading.remove>Entrar no Portal</span>
            <span wire:loading>Entrando...</span>
        </button>
    </form>
    
    <div class="mt-6 text-center">
        <p class="text-sm text-slate-500">Ainda não tem conta? <a href="{{ route('portal.register', ['plan' => $plan]) }}" class="text-primary font-bold hover:underline">Cadastre-se</a></p>
    </div>
</div>
