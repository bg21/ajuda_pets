<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Financeiro;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.tutor')] class extends Component {
    public $faturas = [];

    public function mount()
    {
        $tutor_id = Auth::guard('tutor')->id();
        
        $this->faturas = Financeiro::with(['agendamento.pet', 'venda'])
            ->where(function($query) use ($tutor_id) {
                $query->whereHas('agendamento.pet', function($q) use ($tutor_id) {
                    $q->where('tutor_id', $tutor_id);
                })->orWhereHas('venda', function($q) use ($tutor_id) {
                    $q->where('tutor_id', $tutor_id);
                });
            })
            ->where('tipo', 'receita') // Apenas o que foi cobrado do tutor
            ->orderBy('data_vencimento', 'desc')
            ->orderBy('id', 'desc')
            ->get();
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
        <div style="font-size: 18px; font-weight: 800; color: #191919;">Financeiro</div>
        <div style="width: 20px;"></div> <!-- Spacer -->
    </div>

    <div style="padding: 20px;">
        <h2 style="font-size: 20px; font-weight: 800; color: #191919; margin: 0 0 8px 0;">Minhas Faturas</h2>
        <p style="font-size: 14px; color: #878787; margin: 0 0 24px 0;">Acompanhe aqui o histórico de pagamentos e faturas em aberto na clínica.</p>

        @forelse($faturas as $fatura)
            <div style="background: #fff; border-radius: 16px; padding: 16px; margin-bottom: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); border: 1px solid #EFEFEF; display: flex; flex-direction: column;">
                
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: {{ $fatura->status === 'pago' ? '#E8F5E9' : ($fatura->status === 'cancelado' ? '#FEE2E2' : '#FFF8E1') }}; display: flex; align-items: center; justify-content: center; color: {{ $fatura->status === 'pago' ? '#2E7D32' : ($fatura->status === 'cancelado' ? '#DC2626' : '#F59E0B') }};">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <div style="font-weight: 800; color: #191919; font-size: 16px;">
                                @if($fatura->agendamento)
                                    Consulta Médica ({{ explode(' ', trim($fatura->agendamento->pet->nome))[0] }})
                                @elseif($fatura->venda)
                                    Compra no Petshop
                                @else
                                    {{ $fatura->descricao ?: 'Serviço Clínico' }}
                                @endif
                            </div>
                            <div style="color: #878787; font-size: 13px;">
                                Vencimento: {{ \Carbon\Carbon::parse($fatura->data_vencimento ?? $fatura->created_at)->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 16px; font-weight: 800; color: #191919;">R$ {{ number_format($fatura->valor, 2, ',', '.') }}</div>
                        @if($fatura->status === 'pago')
                            <span style="display: inline-block; padding: 2px 8px; background: #E8F5E9; color: #2E7D32; border-radius: 6px; font-size: 11px; font-weight: 700; margin-top: 4px;">PAGO</span>
                        @elseif($fatura->status === 'pendente')
                            <span style="display: inline-block; padding: 2px 8px; background: #FFF3E0; color: #E65100; border-radius: 6px; font-size: 11px; font-weight: 700; margin-top: 4px;">PENDENTE</span>
                        @else
                            <span style="display: inline-block; padding: 2px 8px; background: #FEE2E2; color: #DC2626; border-radius: 6px; font-size: 11px; font-weight: 700; margin-top: 4px;">CANCELADO</span>
                        @endif
                    </div>
                </div>

                @if($fatura->status === 'pendente')
                    <div style="border-top: 1px dashed #EFEFEF; padding-top: 12px; margin-top: 4px;">
                        <button style="width: 100%; padding: 12px; background: #299D91; color: #fff; border: none; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;" onclick="alert('Integração com Asaas Pix em desenvolvimento!')">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Pagar Fatura com Pix
                        </button>
                    </div>
                @endif
                
            </div>
        @empty
            <div style="text-align: center; padding: 40px 20px; background: #fff; border-radius: 16px; border: 1px dashed #EFEFEF;">
                <svg width="40" height="40" fill="none" stroke="#CBD5E1" viewBox="0 0 24 24" stroke-width="1.5" style="margin: 0 auto 12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p style="font-size: 15px; font-weight: 700; color: #191919; margin: 0 0 4px 0;">Tudo em dia!</p>
                <p style="font-size: 13px; color: #878787; margin: 0;">Você não possui nenhum registro financeiro na clínica ainda.</p>
            </div>
        @endforelse

    </div>
</div>
