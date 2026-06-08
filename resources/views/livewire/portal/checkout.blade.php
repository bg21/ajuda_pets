<?php

use function Livewire\Volt\{state, mount, rules};
use App\Services\AsaasService;
use Illuminate\Support\Facades\Auth;

state([
    'plan' => 'pro', // pro ou max
    'cpf' => '',
    'loading' => false,
    'errorMessage' => null,
]);

\Livewire\Volt\layout('components.layouts.app');

rules([
    'cpf' => 'required|string|min:11',
]);

mount(function () {
    $plan = request()->query('plan', 'pro');
    if (in_array($plan, ['pro', 'max'])) {
        $this->plan = $plan;
    }
});

$irParaPagamento = function (AsaasService $asaasService) {
    $this->validate();
    $this->loading = true;
    $this->errorMessage = null;

    $user = Auth::user();
    $cpfLimpo = preg_replace('/[^0-9]/', '', $this->cpf);

    // Cria o cliente se precisar
    $customerId = $asaasService->getOrCreateCustomer($user, $cpfLimpo);
    
    if (!$customerId) {
        $this->errorMessage = "Erro ao registrar cliente no gateway de pagamento.";
        $this->loading = false;
        return;
    }

    // Cria a assinatura usando o Checkout Hosted do Asaas
    $subscriptionResult = $asaasService->createSubscription($user, $this->plan);

    if (!$subscriptionResult['success']) {
        $this->errorMessage = "Erro ao gerar assinatura: " . $subscriptionResult['message'];
        $this->loading = false;
        return;
    }

    // Redireciona o usuário para o Checkout Seguro da Asaas
    return redirect()->away($subscriptionResult['invoiceUrl']);
};

?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <a href="{{ route('portal.dashboard') }}" class="text-teal-600 hover:text-teal-800 font-medium flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Voltar ao Dashboard
        </a>
    </div>

    <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden">
        <div class="p-8 md:p-10 text-center bg-slate-50 border-b border-slate-100">
            <div class="flex justify-center mb-6">
                <div class="flex items-center gap-3 bg-white px-6 py-3 rounded-2xl shadow-sm border border-slate-200">
                    <span class="font-black text-slate-800">AjudaPet</span>
                    <span class="text-slate-300">|</span>
                    <span class="font-bold text-blue-600">Asaas Checkout Seguro</span>
                </div>
            </div>
            <h1 class="text-3xl font-black text-slate-900 mb-2">Finalizar Assinatura</h1>
            <p class="text-slate-500 font-medium">Você será redirecionado para o ambiente seguro do Asaas para escolher a forma de pagamento (PIX, Boleto ou Cartão).</p>
        </div>

        <div class="p-8 md:p-10">
            @if($errorMessage)
                <div class="mb-8 p-4 bg-red-50 text-red-700 rounded-xl font-medium flex items-center gap-3">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $errorMessage }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Resumo do Pedido -->
                <div>
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Resumo do Pedido</h3>
                    
                    <div class="bg-teal-50 border border-teal-100 rounded-2xl p-6 relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-24 h-24 bg-teal-500 opacity-10 rounded-full blur-xl"></div>
                        <h4 class="font-bold text-teal-800 text-lg mb-1">AjudaPet {{ ucfirst($plan) }}</h4>
                        <p class="text-teal-600 text-sm mb-4">
                            {{ $plan === 'pro' ? 'Até 10 pets' : 'Pets ilimitados' }} • Cobrança Mensal
                        </p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-2xl font-bold text-slate-700">R$</span>
                            <span class="text-4xl font-black text-slate-900">{{ $plan === 'pro' ? '5,00' : '9,99' }}</span>
                            <span class="text-slate-500 text-sm ml-1">/mês</span>
                        </div>
                    </div>
                </div>

                <!-- Dados Iniciais -->
                <div>
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Seus Dados</h3>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-slate-700 mb-2">CPF (Apenas números)</label>
                        <input type="text" wire:model="cpf" placeholder="000.000.000-00" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-medium focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition-all">
                        @error('cpf') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        <p class="text-xs text-slate-400 mt-2">Precisamos do seu CPF apenas para gerar a cobrança oficial no gateway. O resto será preenchido no próximo passo.</p>
                    </div>

                    <button wire:click="irParaPagamento" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-4 px-6 rounded-xl transition-all shadow-lg shadow-teal-600/30 flex justify-center items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="irParaPagamento">Ir para Pagamento Seguro</span>
                        <span wire:loading wire:target="irParaPagamento">Redirecionando...</span>
                    </button>
                    
                    <div class="mt-4 flex items-center justify-center gap-2 text-xs font-bold text-slate-400">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Ambiente Seguro Asaas
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Full Screen Loading Overlay -->
    <div wire:loading wire:target="irParaPagamento" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-all">
        <div class="bg-white p-8 rounded-[2rem] shadow-2xl flex flex-col items-center max-w-sm w-full mx-4 transform transition-all scale-100">
            <svg class="w-16 h-16 text-teal-600 animate-spin mb-6" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <h3 class="text-2xl font-black text-slate-900 mb-2">Ambiente Seguro</h3>
            <p class="text-slate-500 text-center font-medium">Preparando sua conexão com o checkout blindado da Asaas. Por favor, aguarde e não feche a página...</p>
        </div>
    </div>
</div>
