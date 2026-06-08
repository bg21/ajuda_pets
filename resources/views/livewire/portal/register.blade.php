<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

new #[Layout('components.layouts.guest')] class extends Component {
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $plan = 'pro';

    public function mount()
    {
        $this->plan = request()->query('plan', 'pro');
    }

    public function register()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'plan_type' => 'free', // Começa free, e vira pro/max no checkout
        ]);

        Auth::login($user);

        // Se veio da página de planos (querendo assinar)
        if (in_array($this->plan, ['pro', 'max'])) {
            return redirect()->route('portal.checkout', ['plan' => $this->plan]);
        }

        return redirect()->route('portal.dashboard');
    }
}; ?>

<div class="w-full max-w-md bg-white p-8 rounded-3xl shadow-xl border border-slate-100">
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-primary rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-700/20 text-white">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Criar Conta</h1>
        <p class="text-slate-500 mt-1">
            @if($plan === 'pro') Assinar Plano Pro @elseif($plan === 'max') Assinar Plano Max @else Junte-se ao AjudaPet @endif
        </p>
    </div>

    <form wire:submit="register" class="space-y-4">
        <div>
            <label class="block text-sm font-semibold text-slate-600 mb-1">Nome Completo</label>
            <input type="text" wire:model="name" class="w-full border border-slate-200 bg-slate-50 rounded-xl px-4 py-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" required>
            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-600 mb-1">E-mail</label>
            <input type="email" wire:model="email" class="w-full border border-slate-200 bg-slate-50 rounded-xl px-4 py-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" required>
            @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-600 mb-1">Senha</label>
            <input type="password" wire:model="password" class="w-full border border-slate-200 bg-slate-50 rounded-xl px-4 py-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" required>
            @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-600 mb-1">Confirmar Senha</label>
            <input type="password" wire:model="password_confirmation" class="w-full border border-slate-200 bg-slate-50 rounded-xl px-4 py-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" required>
        </div>

        <button type="submit" class="w-full bg-primary hover:bg-emerald-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-emerald-500/30 transition transform hover:-translate-y-0.5 mt-4">
            <span wire:loading.remove>Continuar</span>
            <span wire:loading>Criando conta...</span>
        </button>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm text-slate-500">Já tem uma conta? <a href="{{ route('portal.login', ['plan' => $plan]) }}" class="text-primary font-bold hover:underline">Fazer login</a></p>
    </div>
</div>
