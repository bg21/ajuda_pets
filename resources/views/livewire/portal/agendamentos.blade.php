<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Agendamento;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.tutor')] class extends Component {
    public $proximos = [];
    public $historico = [];

    public function mount()
    {
        $tutor_id = Auth::guard('tutor')->id();
        
        $agendamentos = Agendamento::with(['pet', 'veterinario'])
            ->whereHas('pet', function($query) use ($tutor_id) {
                $query->where('tutor_id', $tutor_id);
            })
            ->where('status', '!=', 'cancelado')
            ->orderBy('data_hora', 'asc')
            ->get();

        $this->proximos = $agendamentos->filter(function($ag) {
            return $ag->data_hora >= now()->startOfDay();
        })->values();

        $this->historico = $agendamentos->filter(function($ag) {
            return $ag->data_hora < now()->startOfDay();
        })->sortByDesc('data_hora')->values();
    }
}; ?>

<div class="pet-desktop-container" style="display: flex; flex-direction: column; background: #F8F9FA; min-height: 100%;">
    
    <style>
        @media (min-width: 768px) {
            .mobile-header { display: none !important; }
            .pet-desktop-container { padding: 40px; max-width: 1000px; margin: 0 auto; background: transparent !important; }
        }
    </style>

    <!-- Top Header c/ Botão Voltar (Apenas Mobile) -->
    <div class="mobile-header" style="padding: 24px 20px 16px 20px; display: flex; justify-content: space-between; align-items: center; background: #fff;">
        <a href="{{ route('portal.dashboard') }}" style="color: #191919; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 600; font-size: 15px;">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Voltar
        </a>
        <div style="font-size: 18px; font-weight: 800; color: #191919;">Agenda</div>
        <div style="width: 20px;"></div> <!-- Spacer -->
    </div>

    <div style="padding: 20px;">
        
        <!-- Próximos Agendamentos -->
        <h2 style="font-size: 20px; font-weight: 800; color: #191919; margin: 0 0 16px 0;">Próximos Agendamentos</h2>
        
        @forelse($proximos as $ag)
            <div style="background: linear-gradient(135deg, #299D91 0%, #0F766E 100%); border-radius: 16px; padding: 20px; margin-bottom: 24px; color: #fff; box-shadow: 0 10px 25px rgba(41, 157, 145, 0.25); position: relative; overflow: hidden;">
                <!-- Decorativo -->
                <svg style="position: absolute; right: -20px; bottom: -20px; opacity: 0.1; width: 120px; height: 120px;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>

                <div style="display: flex; justify-content: space-between; align-items: flex-start; position: relative; z-index: 1;">
                    <div>
                        <div style="background: rgba(255,255,255,0.2); display: inline-block; padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 700; margin-bottom: 12px;">
                            {{ ucfirst($ag->data_hora->translatedFormat('l, d/m/Y')) }} às {{ $ag->data_hora->format('H:i') }}
                        </div>
                        <h3 style="margin: 0 0 4px 0; font-size: 20px; font-weight: 800;">Retorno: {{ explode(' ', trim($ag->pet->nome))[0] }}</h3>
                        <p style="margin: 0; font-size: 14px; opacity: 0.9;">Dr(a). {{ explode(' ', trim($ag->veterinario->nome))[0] }}</p>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800;">
                        {{ substr($ag->pet->nome, 0, 1) }}
                    </div>
                </div>

                @if($ag->observacoes)
                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.2); position: relative; z-index: 1;">
                    <p style="margin: 0; font-size: 13px; opacity: 0.9;"><strong>Obs:</strong> {{ $ag->observacoes }}</p>
                </div>
                @endif
            </div>
        @empty
            <div style="text-align: center; padding: 32px 20px; background: #fff; border-radius: 16px; border: 1px dashed #EFEFEF; margin-bottom: 24px;">
                <svg width="40" height="40" fill="none" stroke="#CBD5E1" viewBox="0 0 24 24" stroke-width="1.5" style="margin: 0 auto 12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <p style="font-size: 15px; font-weight: 700; color: #191919; margin: 0 0 4px 0;">Nenhum agendamento futuro</p>
                <p style="font-size: 13px; color: #878787; margin: 0;">Você não possui consultas marcadas para os próximos dias.</p>
                
                <button style="margin-top: 16px; padding: 10px 20px; background: #E8F5F3; color: #299D91; border: none; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer;">
                    Solicitar Agendamento
                </button>
            </div>
        @endforelse

        <!-- Histórico Passado -->
        <h2 style="font-size: 18px; font-weight: 800; color: #191919; margin: 32px 0 16px 0;">Histórico</h2>
        
        @forelse($historico as $ag)
            <div style="background: #fff; border-radius: 16px; padding: 16px; margin-bottom: 12px; border: 1px solid #EFEFEF; display: flex; align-items: center; gap: 16px;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: #F8F9FA; display: flex; align-items: center; justify-content: center; color: #64748B;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 700; color: #191919; font-size: 15px;">Consulta de {{ explode(' ', trim($ag->pet->nome))[0] }}</div>
                    <div style="color: #878787; font-size: 13px; margin-top: 2px;">
                        {{ $ag->data_hora->format('d/m/Y') }} com Dr(a). {{ explode(' ', trim($ag->veterinario->nome))[0] }}
                    </div>
                </div>
            </div>
        @empty
            <div style="font-size: 13px; color: #9F9F9F; padding: 10px 0;">Nenhum histórico encontrado.</div>
        @endforelse

    </div>
</div>
