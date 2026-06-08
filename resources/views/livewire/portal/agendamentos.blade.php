<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Vaccine;
use Illuminate\Support\Facades\Auth;

new #[Layout('components.layouts.app')] class extends Component {
    public $proximasVacinas = [];
    public $historicoVacinas = [];

    public function mount()
    {
        $user_id = Auth::id();
        
        $vacinas = Vaccine::with('pet')
            ->whereHas('pet', function($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->orderBy('next_due_date', 'asc')
            ->get();

        // Próximas vacinas (onde next_due_date >= hoje)
        $this->proximasVacinas = $vacinas->filter(function($vacina) {
            return $vacina->next_due_date && $vacina->next_due_date >= now()->startOfDay();
        })->values();

        // Histórico de vacinas (ordenado pela data em que foi dada)
        $historico = Vaccine::with('pet')
            ->whereHas('pet', function($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->whereNotNull('date_given')
            ->orderBy('date_given', 'desc')
            ->get();
            
        $this->historicoVacinas = $historico;
    }
}; ?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-slot name="title">Lembretes e Vacinas</x-slot>

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-slate-800">Lembretes & Vacinas</h1>
            <p class="text-slate-500 mt-1 font-medium">Acompanhe as próximas datas de vacinação e o histórico de imunização dos seus pets.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Próximas Vacinas -->
        <div class="lg:col-span-2 space-y-6">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Próximos Vencimentos
            </h2>
            
            @forelse($proximasVacinas as $vacina)
                <div class="bg-gradient-to-br from-teal-500 to-teal-700 rounded-2xl p-6 shadow-md text-white relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full blur-2xl -mr-10 -mt-10 pointer-events-none transition-transform group-hover:scale-110"></div>
                    
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <div class="inline-block bg-white/20 px-3 py-1 rounded-lg text-xs font-bold tracking-wider uppercase mb-3">
                                Vence em {{ $vacina->next_due_date->format('d/m/Y') }}
                            </div>
                            <h3 class="text-2xl font-black mb-1">{{ $vacina->name }}</h3>
                            <p class="text-teal-100 font-medium flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                Pet: <span class="text-white font-bold">{{ $vacina->pet->name }}</span>
                            </p>
                        </div>
                        
                        <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center shadow-inner backdrop-blur-sm">
                            @if($vacina->pet->photo_path)
                                <img src="{{ Storage::url($vacina->pet->photo_path) }}" alt="{{ $vacina->pet->name }}" class="w-full h-full object-cover rounded-2xl">
                            @else
                                <span class="text-xl font-black text-white">{{ substr($vacina->pet->name, 0, 1) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-10 border border-slate-200 border-dashed text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1">Tudo em dia!</h3>
                    <p class="text-slate-500 font-medium">Nenhuma vacina próxima do vencimento encontrada.</p>
                </div>
            @endforelse
        </div>

        <!-- Histórico Passado -->
        <div class="space-y-6">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Histórico
            </h2>
            
            <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100">
                @forelse($historicoVacinas as $vacina)
                    <div class="flex gap-4 {{ !$loop->last ? 'mb-6 pb-6 border-b border-slate-100' : '' }}">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center flex-shrink-0 border border-emerald-100">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $vacina->name }}</p>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">Pet: <span class="text-slate-700">{{ $vacina->pet->name }}</span></p>
                            <div class="flex gap-2 mt-2">
                                <span class="bg-slate-50 text-slate-500 text-[10px] font-bold px-2 py-0.5 rounded-md border border-slate-200">
                                    Aplicada: {{ $vacina->date_given->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6">
                        <p class="text-slate-400 text-sm font-medium">Nenhum histórico de vacinação registrado.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
