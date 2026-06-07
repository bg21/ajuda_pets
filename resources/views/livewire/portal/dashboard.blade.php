<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    //
}; ?>

<div class="space-y-6 pb-10">
    <x-slot name="title">Dashboard</x-slot>

    <!-- Cabeçalho Principal -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Olá, {{ Auth::user()->name ?? 'Tutor' }}! 👋</h2>
            <p class="text-slate-500 text-base mt-1">Acompanhe a saúde e o desenvolvimento dos seus pets.</p>
        </div>
        <button class="bg-accent hover:bg-accent-dark text-white font-semibold px-6 py-2.5 rounded-xl shadow-md transition-all flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Adicionar Pet</span>
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- COLUNA PRINCIPAL -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Cartão do Perfil do Pet -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col md:flex-row items-center gap-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-50 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none"></div>
                
                <div class="relative shrink-0">
                    <div class="w-28 h-28 rounded-xl overflow-hidden shadow-sm border border-slate-100">
                        <img src="https://images.unsplash.com/photo-1543466835-00a7907e9de1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Dog" class="w-full h-full object-cover">
                    </div>
                </div>

                <div class="flex-1 z-10 w-full text-center md:text-left">
                    <div class="flex flex-col md:flex-row md:items-center gap-3 mb-1 justify-center md:justify-start">
                        <h3 class="text-xl font-bold text-slate-800">Max</h3>
                        <span class="bg-emerald-50 text-primary border border-emerald-100 font-semibold px-2 py-0.5 rounded-lg text-xs">Golden Retriever</span>
                    </div>
                    <p class="text-slate-500 text-sm mb-4">Macho • 3 anos e 2 meses • Dourado</p>
                    
                    <div class="flex justify-center md:justify-start gap-6">
                        <div>
                            <p class="text-xs text-slate-400 font-semibold uppercase mb-1">Peso Atual</p>
                            <p class="text-lg font-bold text-slate-800">32.5 <span class="text-xs font-normal text-slate-500">kg</span></p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-semibold uppercase mb-1">Próx. Vacina</p>
                            <p class="text-lg font-bold text-slate-800">15 Out</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Acompanhamento de Peso -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Histórico de Peso</h3>
                    <button class="text-sm font-medium text-primary hover:text-white hover:bg-primary transition bg-emerald-50 px-4 py-2 rounded-lg border border-emerald-100">
                        + Registrar
                    </button>
                </div>
                
                <div class="relative h-48 w-full flex items-end justify-between px-4 pb-6 pt-4 border-b border-slate-100">
                    <!-- Linha principal SVG -->
                    <svg class="absolute inset-0 h-full w-full pointer-events-none" preserveAspectRatio="none" viewBox="0 0 100 100">
                        <path d="M0 80 Q 25 70, 50 40 T 100 20" fill="none" stroke="var(--color-primary)" stroke-width="2" stroke-linecap="round"></path>
                    </svg>

                    <!-- Pontos -->
                    <div class="flex flex-col items-center z-10 w-8">
                        <div class="w-3 h-3 bg-white border-2 border-primary rounded-full mb-4"></div>
                        <span class="text-xs font-medium text-slate-400">Jan</span>
                    </div>
                    <div class="flex flex-col items-center z-10 w-8">
                        <div class="w-3 h-3 bg-white border-2 border-primary rounded-full mb-[30px]"></div>
                        <span class="text-xs font-medium text-slate-400">Fev</span>
                    </div>
                    <div class="flex flex-col items-center z-10 w-8">
                        <div class="w-3 h-3 bg-white border-2 border-primary rounded-full mb-[60px]"></div>
                        <span class="text-xs font-medium text-slate-400">Mar</span>
                    </div>
                    <div class="flex flex-col items-center z-10 w-8">
                        <div class="w-3 h-3 bg-primary ring-2 ring-primary/20 rounded-full mb-[85px]"></div>
                        <span class="text-xs font-bold text-primary">Abr</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- COLUNA LATERAL (Lembretes) -->
        <div class="space-y-6">
            
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Lembretes</h3>
                    <span class="bg-orange-50 text-accent font-semibold text-xs px-2 py-1 rounded-md border border-orange-100">2 Pendentes</span>
                </div>

                <div class="space-y-3">
                    <div class="p-3.5 rounded-xl bg-orange-50/50 border border-orange-100 flex items-center space-x-3 cursor-pointer hover:bg-orange-50 transition">
                        <div class="w-12 h-12 rounded-lg bg-white flex flex-col items-center justify-center text-accent shrink-0 border border-orange-100/50">
                            <span class="text-[10px] font-bold uppercase text-orange-400/80 mb-0.5">Out</span>
                            <span class="text-lg font-bold leading-none">15</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-slate-800 text-sm">Vacina V10</h4>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">Max</p>
                        </div>
                        <div class="w-2 h-2 rounded-full bg-accent shrink-0"></div>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center space-x-3 cursor-pointer hover:bg-slate-100 transition">
                        <div class="w-12 h-12 rounded-lg bg-white border border-slate-100 flex flex-col items-center justify-center text-slate-500 shrink-0">
                            <span class="text-[10px] font-bold uppercase text-slate-400 mb-0.5">Out</span>
                            <span class="text-lg font-bold leading-none">20</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-slate-800 text-sm">Pesagem</h4>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">Max</p>
                        </div>
                    </div>
                </div>

                <button class="w-full mt-4 py-2.5 rounded-lg font-medium text-slate-500 hover:text-slate-700 hover:bg-slate-50 transition border border-dashed border-slate-200 text-sm">
                    Ver Calendário
                </button>
            </div>

        </div>
    </div>
</div>
